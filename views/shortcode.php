<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.serializeJSON/3.2.1/jquery.serializejson.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js"></script>
<script src="https://unpkg.com/htmx.org@1.6.1"></script>
<script src="https://unpkg.com/htmx.org@1.9.12/dist/ext/client-side-templates.js"></script>
<script src="https://cdn.jsdelivr.net/npm/mustache@4.2.0/mustache.min.js"></script>
<script src="https://unpkg.com/hyperscript.org@0.9.5"></script>
<script src="https://unpkg.com/htmx.org/dist/ext/json-enc.js"></script>
<script src="<?= $options['server_url'].'/js/api.js'?>"></script>
<script src="<?= $options['server_url'].'/js/lib/stomp.js'?>"></script>
<div id="settings" <?php foreach(array_merge($options,$args) as $key=>$val){
    echo $key.'='.'"'.$val.'"';
}?>></div>
<h1 class="chat-header">Чат с поддержкой</h1>
<div class="chat-container">
    <div id="messages"></div>
    <div>
        <input type="text" id="messageInput">
        <button id="sendBtn">Отправить</button>
    </div>
</div>

<style>
    #messageInput,#sendBtn{
        border: 1px solid;
    }
</style>
<script>
    const App = function(){
        this.init = async () => {
            let $settings = $('#settings');
            this.getSetting = (name) =>  $settings.attr(name);
            
            this.user={
                login: this.getSetting('user'),
                password: this.getSetting('pass')
            }
            this.onError = (error)=>{
                if(error.error=='UserExistsException') {
                    console.log('user exists'); return;
                }
                if(error.error=='TokenExpiredException') {
                    window.location.reload();
                }
                if (error.message) error = error.message;
                console.log(error);
                alert(error);
            }
            this.onConnect = ()=>{
               
              
                this.api.subscribeOnChats((m) => {
                    let op = m.operation;
                    let data = m.data;
                    
                    if (op === "DELETE") {
                        if(data.id==this.currentChatId) this.currentChatId=null;
                    }
                });
                
            }
            this.receiveMsg = (m)=>{
                let op = m.operation;
                let data = m.data;
                if (op === "ADD") {
                    this.onAddMsg(data);
                }
                else if (op === "DELETE") {
                    this.onDeleteMsg(data);
                }
            }
            
            this.renderMessage= (msg)=>{
                $msgHtml = `
                     <div class="message-item" data-id="${msg.id}">
                        <span class="message-sender"> ${msg.sender}:  </span>
                          <span class="message-text"> ${msg.message}</span>
                        <span class="message-time">(${msg.sendTime})</span>
                    </div>`
                return $msgHtml;
            }
            this.onAddMsg = (msg)=>{
                appendListItem('#messages', this.renderMessage(msg))
            }
            this.onDeleteMsg = (data) => {
                removeItem(`.message-item[data-id=${data.id}]`);
            }
            
            this.initializeChat = async (create=true) => {
                let chatName = this.getSetting('chat_prefix')+this.user.login;
                let chats = await this.api.findChats(chatName);
                let chat = {};
                
                if(chats && chats.length>0){
                    chat = await this.api.getChat(chats[0].id);
                    this.currentChatId = chat.id;
                    // Load existing messages
                    let messages = await this.api.getMessagesFromChat(this.currentChatId);
                    messages.forEach(msg => {
                        appendListItem('#messages', this.renderMessage(msg))
                    });
                } else if(!create){
                    return;
                }
                else {
                    let adminLogin = this.getSetting('admin');
                    chat = await this.api.createChat({name:chatName, users:[adminLogin]});
                    let admin = (await this.api.findUsersInChat(chat.id,adminLogin))[0];
                    await this.api.transferChatOwnership(chat.id,admin.id);
                    this.currentChatId = chat.id;
                }
                
                this.api.subscribeOnMessagesInChat(this.currentChatId, (msg) => this.receiveMsg(msg));
            }
            
            this.sendMessage = async () => {
                let message = $('#messageInput').val();
                if (!message) return;
                
                // Если чат еще не создан
                if (!this.currentChatId) {
                    try {
                        await this.initializeChat();
                    } catch (err) {
                        this.onError(err);
                        return;
                    }
                }
                
                this.api.sendMessageToChat(this.currentChatId, message);
                $('#messageInput').val('');
            }
           
            this.api = new MessengerApi({
                serverUrl: this.getSetting('server_url'), 
                brokerUrl: this.getSetting('socket_url'), 
                onConnect: this.onConnect,
                onError: this.onError
            });
            
            this.auth = async () => {
                this.token = await this.api.authUser(this.user);
                if (!this.token) return;
                this.api.init(this.token);
            }

            try {
                await this.api.registerUser(this.user);
            } catch(err) {
                console.log('already registered');
            }
            
            await this.auth();
            this.api.socketClientConnect();
            await this.initializeChat(false); // Initialize chat after authentication

            function appendListItem(listName, listItemHTML) {
                $(listItemHTML)
                    .hide()
                    .css('opacity', 0.0)
                    .appendTo(listName)
                    .slideDown(100)
                    .animate({ opacity: 1.0 })
            }

            function removeItem(name) {
                $(name).fadeOut(300, function () { $(this).remove(); });
            }
        }
    };
    
    $(window).on('load', () => {
        app = new App();
        app.init();
        $('#sendBtn').on('click', () => app.sendMessage());
        $('#messageInput').on('keypress', (e) => {
            if (e.which === 13) app.sendMessage();
        });
    });
</script>