<?php
/*
Plugin Name:    VHAP
Plugin URI:     https://vahro.ru/blog/plugins/vhap-administration-plugin-template
Description:    VHAP - Administration plugin page template
Author:         vahro
Author URI:     https://vahro.ru/
Version:        1.0.0
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

  add_settings_section(
    'vhap_section_id', // ID секции, при добавлении поля (add_settings_field) ссылаемся на ID секции в которую добавляем
    'Настройка плагина', // заголовок (не обязательно)
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
      'vhap_section_id', // Id секции где размещается поле
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
      'vhap_section_id', // Id секции где размещается поле
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
          do_settings_sections( 'vhap_page' ); // ярлык страницы, не более
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
    add_options_page( 'VHAP', 'VHAP', 'manage_options', 'vhap', 'vhap_options_html' );
    return;
  }

  // Top Level Menu; 'vhap_toplevelmenu' => 'top'
  add_menu_page (
    'Плагин VHAP', // заголовок страницы - будем выводить его в <h1></h1>
    'VHAP', // текст ссылки в меню - будем выводить в основное меню админки WP слева
    'manage_options', // права пользователя, необходимые для доступа к странице - manage_options - administrator
    'vhap', // page id - https://site.ru/wp-admin/admin.php?page=vhap
    'vhap_options_html', // callback - функция, которая выводит содержимое страницы
    );
  // повторяется пункт из основного меню - одинаковый callback - vhap_options_html
  add_submenu_page ( 
    'vhap',
    'Плагин VHAP',
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


add_action( 'admin_init', function() { 
//  error_log( 'admin_init' );
});

add_action( 'admin_bar_menu', function() { 
//  error_log( 'admin_bar_menu' );
});
