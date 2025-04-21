<div class="section-<?php echo $pluginName ?>"><!-- $pluginName - задает название секции для разграничения JS скрипта -->

  <!-- Тут начинается верстка видимой части станицы настроек плагина-->
  <div class="widget-body">

    <!-- Панель с настройками (поля заполняются в соответствии с установленными значениями в опциях плагина)-->
    <div class="property-order-container widget-panel-content">
      <h2><?php echo "Настройки подключения"; ?>:</h2>
      <?php if ($error): ?><span class="error">Ошибка: <?= $error ?></span><?php endif; ?>
      
      <form class="base-setting" name="base-setting" method="POST">
        <ul class="list-option"> <!-- список полей для редактирования опций -->
          <li>
            <label>
              <span class="custom-text">Администратор:<a class="tool-tip-top fa fa-question-circle fl-right" title="Логин администратора системы"></a></span>
              <input type="text" name="admin" placeholder="admin" id="admin" value="<?php echo $options['admin'] ?>">
            </label>
          </li>
          
          <li>
            <label>
              <span class="custom-text">URL сервера:<a class="tool-tip-top fa fa-question-circle fl-right" title="Базовый URL HTTP сервера"></a></span>
              <input type="text" name="server_url" placeholder="http://localhost:8080" id="server_url" value="<?php echo $options['server_url'] ?>">
            </label>
          </li>
          
          <li>
            <label>
              <span class="custom-text">WebSocket URL:<a class="tool-tip-top fa fa-question-circle fl-right" title="URL для WebSocket подключения"></a></span>
              <input type="text" name="socket_url" placeholder="ws://localhost:8080/ws" id="socket_url" value="<?php echo $options['socket_url'] ?>">
            </label>
          </li>
          
          <li>
            <label>
              <span class="custom-text">Префикс чатов:<a class="tool-tip-top fa fa-question-circle fl-right" title="Префикс для идентификаторов чатов"></a></span>
              <input type="text" name="chat_prefix" placeholder="chat_" id="chat_prefix" value="<?php echo $options['chat_prefix'] ?>">
            </label>
          </li>
          
          <li>
            <label>
              <span class="custom-text">Префикс пользователей:<a class="tool-tip-top fa fa-question-circle fl-right" title="Префикс для идентификаторов пользователей"></a></span>
              <input type="text" name="user_prefix" placeholder="user_" id="user_prefix" value="<?php echo $options['user_prefix'] ?>">
            </label>
          </li>
        </ul>
      </form>

      <a role="button" href="javascript:void(0);" class="base-setting-save custom-btn button success">
        <span><i class="fa fa-floppy-o" aria-hidden="true"></i><?php echo "Сохранить"; ?></span>
      </a> <!-- Кнопка сохранения настроек плагина -->
      <div class="clear"></div>
    </div>
  </div>
</div>

