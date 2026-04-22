<?php
/*
Plugin Name:    VHAP
Plugin URI:     https://vahro.ru/blog/plugins/vhap-administration-plugin-template
Description:    VHAP - Administration plugin page template
Author:         vahro
Author URI:     https://vahro.ru/
Version:        1.0.1

Ver 1.0.1
Добавлен вывод икноки плагина в Top Level Menu

Ver 1.0.2
Добавлена опция vhap_icon - варианты "default"/"superhero"/"vahro"
vhap_icon - radio buttons - выбор иконки плагина в секции "Настройки плагина"

*/

defined( 'ABSPATH' ) OR exit; // Game over
// !defined('ABSPATH') && exit; 
// If this file is called directly, abort.
// if ( !class_exists('WP') ) die();
// if ( !defined('WPINC') ) die;


// активация плагина
register_activation_hook( __FILE__, 
  function () {
    // что-то делаем при активации плагина в админке
    if( !get_option('vhap_toplevelmenu') ) {
      // если опции vhap_toplevelmenu не существует, она добавляется в БД (wp_options) со значением 'top' 
      add_option('vhap_toplevelmenu', 'top');
    }
    if( !get_option('vhap_deactivation') ) {
      // если опции vhap_deactivation не существует, она добавляется в БД (wp_options) со значением 'yes' 
      add_option('vhap_deactivation', 'yes');
    }
    if( !get_option('vhap_icon') ) {
      // если опции vhap_icon не существует, она добавляется в БД (wp_options) со значением 'vahro' 
      add_option('vhap_icon', 'vahro');
    }
});

// деактивация плагина
register_deactivation_hook(  __FILE__, 
  function () {
  // что-то делаем при деактивации плагина в админке
});

// удаление плагина
// !!! анонимная функция в этом хуке не работает
register_uninstall_hook( __FILE__, 'vhap_uninstall');
function vhap_uninstall () {
  delete_option( 'vhap_toplevelmenu' );
  delete_option( 'vhap_deactivation' );
  delete_option( 'vhap_icon' );
}

// add_action( 'delete_option_vhap_toplevelmenu', function () { error_log( 'delete_option_vhap_toplevelmenu' ); } );
// add_action( 'delete_option_vhap_deactivation', function () { error_log( 'delete_option_vhap_deactivation' ); } );


// =================================================================================================== Загрузка CSS и JS
add_action( 'admin_enqueue_scripts', function( $hook_suffix ) {
  // error_log('hook_suffix => ' . $hook_suffix); // определяется $hook_suffix
  if ( $hook_suffix == 'toplevel_page_vhap' || $hook_suffix == 'settings_page_vhap' ) {
    // CSS файл загружается только если это страница настройки плагина VHAP в админке
    wp_enqueue_style( 'vhap', plugin_dir_url(__FILE__) . 'vhap.css');
  }
});

// =================================================================================================== register_setting

add_action( 'admin_init', function() { 

  register_setting('vhap_setting', 'vhap_toplevelmenu', ['sanitize_callback' => 'vhap_toplevelmenu_validate',]);
  register_setting('vhap_setting', 'vhap_deactivation', ['sanitize_callback' => 'vhap_deactivation_validate',]);
  register_setting('vhap_setting', 'vhap_icon', ['sanitize_callback' => 'vhap_icon_validate',]);

  add_settings_section(
    'vhap_section_options', // ID секции, при добавлении поля (add_settings_field) ссылаемся на ID секции в которую добавляем
    'Настройка плагина (заголовок устанавливается функцией add_settings_section)', // заголовок (может быть пустой - '')
    '', // функция для вывода HTML секции (необязательно)
    'vhap_page', // ярлык страницы
    array(
      'before_section' => '<fieldset class=%s>',
      'after_section'  => '</fieldset>',
      'section_class'  => 'vhap_section',
    )
  );

// ============================================== vhap_deactivation option - checkbox
  add_settings_field(
      'vhap_deactivation', 
      'VHAP Deactivation', 
      'vhap_deactivation_html', // callback
      'vhap_page',
      'vhap_section_options', // Id секции где размещается поле
      ['name' => 'vhap_deactivation',] 
      );

  function vhap_deactivation_html ( $args ) {
      $field = $args[ 'name' ];
      $option = get_option( $args[ 'name' ] ); // 'vhap_deactivation'
      $html = '<input type="checkbox" name="' . $field . '" value="yes" ' . checked( 'yes', $option, false ) . '/>';
      $html = $html . ' убрать/восстановить пункт "Деактивировать" в списке плагинов в <a href="plugins.php">меню "Плагины"</a>';
      echo $html;
  }

  function vhap_deactivation_validate ( $input ) {
    return( $input != 'yes' ? 'no' : $input );
  }

// ============================================== vhap_toplevelmenu option - checkbox
  add_settings_field(
      'vhap_toplevelmenu', 
      'Top Level Menu', 
      'vhap_toplevelmenu_html', // callback
      'vhap_page',
      'vhap_section_options', // Id секции где размещается поле
      ['name' => 'vhap_toplevelmenu',] 
      );

  function vhap_toplevelmenu_html ( $args ) {
    // Размещение меню 'top'/'sub'
    // 'top' - Top Level Menu
    // 'sub' - submenu
      $field = $args[ 'name' ];
      $option = get_option( $args[ 'name' ] ); // 'vhap_toplevelmenu'
      $html = '<input type="checkbox" name="' . $field . '" value="top" ' . checked( 'top', $option, false ) . '/>';
      $html = $html . ' пункт меню VHAP либо в основном меню слева (Top Level Menu) либо в дополнительном (Submenu) пункта "Настройки"';
      echo $html;
  }
  function vhap_toplevelmenu_validate ( $input ) {
    return( $input != 'top' ? 'sub' : $input );
  }

  // ============================================== vhap_icon option - radio
  add_settings_field(
      'vhap_icon', 
      'Plugin Icon', 
      'vhap_icon_html', // callback
      'vhap_page',
      'vhap_section_options', // Id секции где размещается поле
      ['name' => 'vhap_icon',] 
      );
  function vhap_icon_html( $args ) {
    // $option = get_option('vhap_icon');
    $option_name = $args[ 'name' ];
    $option = get_option( $option_name,  'default' );
    $disabled = ( get_option( 'vhap_toplevelmenu' ) != 'top' ) ? ' disabled ' : '';
    echo "<fieldset $disabled>";
    $items = array( "default", "superhero", "vahro" );
    foreach($items as $item) {
      $checked = ( $option == $item ) ? ' checked ' : '';
      // echo "<label><input " . $disabled . $checked . " value='$item' name='$option_name' type='radio' /> $item </label> ";
      echo "<label><input " . $checked . " value='$item' name='$option_name' type='radio' /> $item </label> ";
    }
    echo "</fieldset>";
  }
  function vhap_icon_validate ( $input ) {
    // error_log('input => "' . $input . '"');
    // Пустой $input может придти когда radio кнопки поставлены в disabled
    // тогда берем значение из базы, если там нет - тогда 'default'
    if ( !$input ) return ( get_option( 'vhap_icon', 'default' ) );
    return( in_array( $input, [ "default", "superhero", "vahro" ] ) ? $input : "default" );
  }
} );

// ================================================================================================= admin_notices
add_action( 'admin_notices', function () {
// https://developer.wordpress.org/reference/hooks/admin_notices/
// После нажатия конпки "Submit" - функция submit_button()
// выводится сообщение на страницу настроек - 'Done!'
// * notice-error – will display the message with a white background and a red left border.
// * notice-warning– will display the message with a white background and a yellow/orange left border.
// * notice-success – will display the message with a white background and a green left border.
// * notice-info – will display the message with a white background a blue left border.
  if (isset( $_GET[ 'settings-updated' ] ) && true == $_GET[ 'settings-updated' ]	) {
  ?>
  <div class="notice notice-success is-dismissible">
    <p><?php _e( 'Done!', 'sample-text-domain' ); ?></p>
  </div>
  <?php }
} );

// ================================================================================================= main html
// Вывод страницы настроек https://vahro.ru/wp-admin/admin.php?page=vhap
function vhap_options_html() {
  ?>
  <div class="wrap">
      <h1><?php echo esc_html( get_admin_page_title() ); // будет выведен заголовок страницы который определен в add_submenu_page ?></h1>
      <form method="post" action="options.php">
        <?php
          // settings_errors( 'vhap_settings_errors' ); // Вывод ошибки на экран здесь, если ошибки есть
          settings_fields( 'vhap_setting' ); // название настроек - https://developer.wordpress.org/reference/functions/settings_fields/
          do_settings_sections( 'vhap_page' ); // slug (ярлык) страницы
          submit_button(); // функция для вывода кнопки submit - https://developer.wordpress.org/reference/functions/submit_button/
       ?>
      </form></div>
  <?php
}

// 
// =================================================================================================== Menu
// 
/**
 * Add the top level menu page.
 */

add_action( 'admin_menu', function() { 

// Регистрируется пункт VHAP в главном меню и несколько пунктов подменю
  global $submenu;

  $option = get_option( 'vhap_toplevelmenu' );
  if ( $option == 'sub' ) {
    // Регистрируется подпункт меню 'VHAP' в пункте основного меню админки "Настройки"
    // 'vhap_toplevelmenu' может быть или 'top' или 'sub'
    // Submenu - пункт "Настройки" - slug - options-general.php
    // add_submenu_page ( 'options-general.php', ...)
    // add_submenu_page( 'options-general.php', 'VHAP', 'VHAP', 'manage_options', 'vhap', 'vhap_options_html' );
    add_options_page( 'VHAP (заголовок устанавливается функцией add_options_page)', 'VHAP', 'manage_options', 'vhap', 'vhap_options_html' );
    return;
  }
  $icon_option = get_option( 'vhap_icon' );
  $icon = "";
  if ( $icon_option == 'superhero' ) $icon = 'dashicons-superhero';
  if ( $icon_option == 'vahro' ) {
    $icon = "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCI+DQo8cGF0aCBmaWxsPSIjYTdhYWFkIiBkPSJNMTkuMDMsMEguOTdDLjQ0LDAsMCwuNDQsMCwuOTdWMTguODVjMCwuNTQsLjQ0LC45NywuOTcsLjk3SDE5LjAzYy41NCwwLC45Ny0uNDQsLjk3LS45N1YuOTdjMC0uNTQtLjQ0LS45Ny0uOTctLjk3Wm0tLjcxLDE1LjQzaC0yLjYzdi00LjAyYzAtLjMzLS4wMi0uNjUtLjA1LS45OC0uMDMtLjMyLS4wOS0uNTYtLjE4LS43Mi0uMS0uMTgtLjI0LS4zMS0uNDMtLjM5LS4xOS0uMDgtLjQ1LS4xMi0uNzgtLjEyLS4yNCwwLS40OCwuMDQtLjcyLC4xMi0uMjUsLjA4LS41MSwuMi0uOCwuMzd2NS43NGgtMi42M1Y3Ljg2bC0yLjk1LDcuNTdoLTIuOTdMMS4wNiw3LjMzSDMuODJsMS45LDUuNTgsMS44Ny01LjU4aDIuNTF2LTMuMTJoMi42M3Y0LjAyYy40Ny0uMzYsLjkxLS42NCwxLjM0LS44M3MuOS0uMjksMS40My0uMjljLjg4LDAsMS41NywuMjYsMi4wNywuNzYsLjUsLjUxLC43NSwxLjI3LC43NSwyLjI5djUuMjdaIi8+DQo8L3N2Zz4=";
  }
    // Top Level Menu; 'vhap_toplevelmenu' => 'top'
  add_menu_page (
    'Плагин VHAP (заголовок устанавливается функцией add_menu_page)', // заголовок страницы - будем выводить его в <h1></h1>
    'VHAP', // текст ссылки в меню - будем выводить в основное меню админки WP слева
    'manage_options', // права пользователя, необходимые для доступа к странице - manage_options - administrator
    'vhap', // page id - https://site.ru/wp-admin/admin.php?page=vhap
    'vhap_options_html', // callback - функция, которая выводит содержимое страницы
    // 'dashicons-superhero', // по умолчанию 'dashicons-admin-generic', посмотреть здесь https://developer.wordpress.org/resource/dashicons/
    $icon, // по умолчанию 'dashicons-admin-generic', посмотреть здесь https://developer.wordpress.org/resource/dashicons/
    );
  // 100 // позиция в меню
  // повторяется пункт из основного меню - одинаковый callback - vhap_options_html
  add_submenu_page ( 
    'vhap',
    'Плагин VHAP (заголовок устанавливается функцией add_submenu_page)',
    'Настройка',
    'manage_options',
    'vhap',
    'vhap_options_html'
    );

  // дополнительный пункт меню - описание (документация) плагина - callback - vhap_doc_html
  add_submenu_page ( 
    'vhap',
    'Описание VHAP',
    'Описание',
    'manage_options',
    'vhap_doc',
    'vhap_doc_html'
    );

  // пункт submenu - ссылка на любой URL
  $permalink = 'https://vahro.ru';
  $submenu['vhap'][] = array( 'vahro', 'manage_options', $permalink );
});

function vhap_doc_html() {
  // vhapdoc.php - документация к плагину, обычный html файл и чуть-чуть php
  include __DIR__ . DIRECTORY_SEPARATOR .'vhapdoc.php';
}



// ================================ ССЫЛКИ в общей таблице плагинов
// Добавим ссылку на страницу настроек в таблицу плагинов на странице плагинов https://site.ru/wp-admin/plugins.php
// Хук plugin_action_links срабатывает при выводе списка плагинов и позволяет добавлять собственные ссылки
$plugin_file = plugin_basename( __FILE__ ); // вернет vhap/vhap.php
add_filter("plugin_action_links_$plugin_file", function ( $links ) {  
    // $option = get_option( 'vhap_deactivation' ); // 'vhap_deactivation'
    if ( get_option( 'vhap_deactivation' ) != 'yes' ) 
      unset( $links['deactivate'] );  // Remove the deactivate link
    if ( get_option( 'vhap_toplevelmenu' ) != 'sub' ) 
      array_unshift( $links, '<a href="admin.php?page=vhap_doc" rel="noopener">Документация</a>' ); 
    array_unshift( $links, '<a href="admin.php?page=vhap">Настройки</a>' ); 
    return $links;
});

