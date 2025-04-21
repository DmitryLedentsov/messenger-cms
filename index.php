<?php

/*
  Plugin Name: Мессенджер
  Description: Плагин в автоматическом режиме предоставляет покупателю указанную скидку на второй заказ. При последующих заказах скидка не предоставляется.
  Author: Belka.one
  Version: 1.0.0
 */

new P4Messenger;

class P4Messenger {

  private static $lang = array(); // массив с переводами плагина (локали)
  private static $pluginName = ''; // название плагина (соответствует названию папки, задается в контроллере)
  private static $path = ''; //путь до файлов плагина (задается в контроллере)

  public static $cronAction = '';
  
  private static $debug = false;
  public function __construct() {
  
    self::$pluginName = PM::getFolderPlugin(__FILE__);
    self::$path = PLUGIN_DIR.self::$pluginName;
    self::$cronAction = self::$pluginName.'-cron';

    mgActivateThisPlugin(__FILE__, array(__CLASS__, 'activate')); 
    mgAddAction(__FILE__, array(__CLASS__, 'pageSettingsPlugin'));
    
    mgAddAction('mg_start', [__CLASS__, 'requestIntercept']);
    mgAddShortcode('p4-messenger', array(__CLASS__, 'handleShortCode'));
    //mgAddAction('models_cart_applycoupon', array(__CLASS__, 'applyCoupon'), 1, 100);
    //mgAddAction('getItemsCart', array(__CLASS__, 'CheckOrder'), 10);
  }
  


  static function activate() {
    self::createDateBase();
    self::createOptions(self::$debug);

   
   
  }

  private static function createDateBase() {   
    DB::query("DROP TABLE IF EXISTS `".PREFIX.self::$pluginName."`");
		/*DB::query("
		CREATE TABLE IF NOT EXISTS `".PREFIX.self::$pluginName."` (
		`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Порядковый номер записи',
		`promocode` text NOT NULL COMMENT 'Промокод',
    `mail_send_date` timestamp COMMENT 'Дата отправки письма',
    `email_to` TEXT COMMENT 'Адрес почты',
    `name_to` TEXT COMMENT 'Имя получателя',
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");*/
	}
  private static function createOptions($override = false) {
    if(MG::getSetting(self::$pluginName.'-option') == null || $override){   
      $array = array( 
      "admin"=>"god",
      "server_url"=>"http://localhost:9087",
      "socket_url"=>"ws://localhost:9087/ws",
      "chat_prefix"=>"chat_",
      "user_prefix"=>"user_"
      );         
      self::setOptions($array);
    }
  }

  public static function getOption($op){
    return self::getOptions()[$op];
  }

  public static function setOptions($array){
    MG::setOption(array('option' => self::$pluginName.'-option', 'value' => addslashes(serialize($array))));
  }

  public static function setOption($key, $val){
    MG::setOption(array('option' => self::$pluginName.'-option', 'value' => addslashes(serialize([$key=>$val]))));
  }
  

  static function pageSettingsPlugin() {
    $pluginName = self::$pluginName;
    $options = self::getOptions();   

    self::preparePageSettings();
	
    include('pageplugin.php');
  }

  private static function preparePageSettings() {
    echo '   
      <link rel="stylesheet" href="'.SITE.'/'.self::$path.'/css/admin.css?'.rand(0,1000).'" type="text/css" />
      <link rel="stylesheet" href="'.SITE.'/'.self::$path.'/css/timepicker.min.css" type="text/css" /> 

      <script>
        includeJS("'.SITE.'/'.self::$path.'/js/script.js"); 
        
        includeJS("'.SITE.'/'.self::$path.'/js/jquery-ui-timepicker-addon.js");
        includeJS("'.SITE.'/'.self::$path.'/js/jquery.simple-color.min.js");
      </script> 
      ';   
  }

  
  public static function getOptions() {  
    $option = MG::getSetting(self::$pluginName.'-option');	
    $option = stripslashes($option);
    $options = unserialize($option);	
    return $options; 
  }

  public static function getBasePromo(){
    $basePromoKey = self::getOption('promocode');
    return self::getPromo($basePromoKey);
  }

  public static function getPromo($key){
    $res = DB::query("SELECT * FROM `".PREFIX."oik-discount-coupon` WHERE TITLE =  ".DB::quote($key));
    $basePromoInfo = DB::fetchAssoc($res);
    return $basePromoInfo;
  }

  public static function getPromoByCode($code){
    $res = DB::query("SELECT * FROM `".PREFIX."oik-discount-coupon` WHERE code =  ".DB::quote($code));
    $basePromoInfo = DB::fetchAssoc($res);
    return $basePromoInfo;
  }


  public static function logFile($file, $data, $append = true) {
    // Если режим отладки выключен, ничего не делаем
    if (!self::$debug) return;

    // Преобразуем данные в строку, если они не являются строкой
    $log = is_string($data) ? $data : print_r($data, true);

    // Определяем путь к поддиректории debug
    $debugDir = __DIR__ . '/debug/';

    // Проверяем, существует ли директория debug, и создаём её, если нет
    if (!is_dir($debugDir)) {
        mkdir($debugDir, 0777, true); // Создаём директорию с правами 0777
    }

    // Формируем полный путь к файлу
    $file = $debugDir . $file;

    // Формируем строку с временной меткой
    $timestamp = "\n" . "====================" . gmdate('Y-m-d h:i:s \G\M\T', time()) . "=====================" . "\n";

    // Записываем данные в файл
    if ($append) {
        file_put_contents($file, $timestamp . $log . "\n", FILE_APPEND);
    } else {
        file_put_contents($file, $log);
    }
}


  public static function requestIntercept()
  {
    $urlLastSection = URL::getLastSection();
    if ($urlLastSection === self::$cronAction) {
      try{
        echo json_encode(self::taskAction());
        exit;
      } catch (Throwable $e) {
        $error = [
          'message'=>$e->getMessage(),
          'trace'=>$e->getTrace()
        ];
       
        self::logFile('error',$error);
        echo json_encode($error);
        exit;
      }
    }
    return;
  }

  //идём последовательно по таблице и проверяем пора ли послать письмо
  public static function taskAction(){
    


    return 'Mails sent';
  
  }

  public static function handleShortCode($args = [])
	{
		if (!$args['user']) return false;
		if (!$args['pass']) return false;

    $options = self::getOptions();
   // $args['user'] = $options['user_prefix'].$args['user'];
		ob_start();
	  include('views/shortcode.php');
	  $html = ob_get_contents();
	  ob_end_clean();

	  return $html;
	}

  
}