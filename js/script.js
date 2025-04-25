 /* 
 * Модуль P4MessengerModule, подключается на странице настроек плагина. Скрипт будет запущен только тогда, когда страница админки будет готова к выводу настроек плагина.
 */

var P4MessengerModule = (function() {
  
  return { 
    pluginName: "p4-messenger", //название плагина
    
    loadScript : function (url, callback) {
      $.ajax({
          url: url,
          dataType: 'script',
          success: callback,
          async: true
      });
    },

    // Функция инициализации, выполняемая, когда страница админки будет готова вывести настройки плагина
    init: function() {  
      console.log("p4-messenger")    ;
      
       
        
   
       // [Клик по кнопке "Сохранить" в панели настроек плагина]: сохраняет настроки плагина
      $('.admin-center').on('click', '.section-'+P4MessengerModule.pluginName+' .base-setting-save', function() {
       
        // Собираем все значения формы в один массив
        var data = {};
        
  
        $.each($('.section-'+P4MessengerModule.pluginName+' .base-setting').serializeArray(),
            function(i, v) {
                data[v.name] = v.value;
            }
        );
        console.log('p4-messenger',data);
        /*
          promocode: $('.section-'+P4MessengerModule.pluginName+' .base-setting .list-option input[name="promocode"]').val(),
        }*/
        console.log(data);
        // Выполняем запрос в Pactioner плагина к методу saveBaseOption()
        admin.ajaxRequest({
          mguniqueurl: "action/saveBaseOption", // действие для выполнения на сервере
          pluginHandler: P4MessengerModule.pluginName, // плагин для обработки запроса
          // входные данные:
          data: data 
        }, function(response) {
          // Выводим нотификацию с результатом (ошибка или успех)
          admin.indication(response.status, response.msg);    
          // Если успех, обновляем страницу настроек плагина  
          if (response.status == "success") {
            admin.refreshPanel()
            location.reload();
          }
        }
        )
      })     
      
      
    },

    loadWidget: function(){
      $('#messenger').empty();
      let serverUrl = $('input[name="server_url"]').val()+'/';
      console.log(serverUrl);
      messengerWidget('messenger',{serverUrl:serverUrl});
    }


  }
})();

// Инициализация скрипта
P4MessengerModule.init()

  let serverUrl = $('input[name="server_url"]').val()+'/';
  P4MessengerModule.loadScript(serverUrl+'js/widget.js', ()=>{
    P4MessengerModule.loadWidget();
  })
 
