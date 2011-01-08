<?php 
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2011 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\misc\rich_text;   
 
if (!defined('CMS')) exit;

class Config // extends MimeType
{
  static function getLayouts()
  {
    global $parametersMod;
    $layouts = array();
    $layouts[] = array('translation'=>$parametersMod->getValue('standard', 'content_management', 'widget_rich_text', 'layout_default'), 'name'=>'default');
    return $layouts;
  }

  static function getMceInit(){
    global $site;
    $site->requireConfig('standard/content_management/config.php');


    //tinymce styles
    $tinyMceStylesStr = '';
    $classesArray = '';
    foreach(\Modules\standard\content_management\Config::getMceStyles() as $style){
      if($tinyMceStylesStr != ''){
        $tinyMceStylesStr .= ';';
      }
      $tinyMceStylesStr .= $style['translation'].'='.$style['css_style'];

      if($style['css_style'] != ''){
        if($classesArray != ''){
          $classesArray .= ',';
        }
        $classesArray .= '"'.$style['css_style'].'"';
      }

    }
    //end tinymce styles

    return '
    tinyMCE.init( {
      theme : "advanced",
      mode : "exact",
      elements : "management_" + collection_number + "_text",
      plugins : "iplink,paste,simplebrowser,safari,spellchecker,pagebreak,style,layer,table,advhr,advimage,emotions,iespell,inlinepopups,media,contextmenu,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

      theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
      theme_advanced_buttons2 : "cut,copy,pastetext,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,forecolor,backcolor",
      theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,fullscreen",
      theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,blockquote,pagebreak,|,insertfile,insertimage",
      theme_advanced_toolbar_location : "top",
      theme_advanced_toolbar_align : "left",

      file_browser_callback : "simplebrowser_browse", 
      theme_advanced_statusbar_location : "bottom",
      theme_advanced_resizing : true,
      theme_advanced_resize_horizontal : false,
      height : "300",
      content_css : "'.BASE_URL.THEME_DIR.THEME.'/ip_content.css",
      theme_advanced_styles : "'.$tinyMceStylesStr.'",
      forced_root_block : "p",

      document_base_url : "'.BASE_URL.'",
      remove_script_host : false,
      relative_urls : false,
      convert_urls : true,

      paste_auto_cleanup_on_paste : true,
      paste_retain_style_properties : false,
      paste_strip_class_attributes : true,
      paste_remove_spans : true,
      paste_remove_styles : true,
      paste_convert_middot_lists : true,

      paste_preprocess : function(pl, o) {
        o.content = o.content.stripScripts();
        var tmpContent = o.content;
        var classesArray = new Array ('.$classesArray.');


        tmpContent = tmpContent.replace(/(<strong>)/ig, "<b>"); /*replace strong with bold*/
        tmpContent = tmpContent.replace(/(<\\/strong>)/ig, "</b>");

        /* remove unknown classes */
        var pattern = /<[^<>]+class="[^"]+"[^<>]*>/gi; /* find all tags containing classes */
        var matches = tmpContent.match(pattern);
        for(var i =0; matches && i < matches.length; i++){ /* loop through found tags */
          var pattern2 = /class="[^"]+"/gi;  /* find class name */
          var matches2 = matches[i].match(pattern2);
          for(var i2 = 0; matches2 && i2 < matches2.length; i2++){ /* throw away unknown classes */
            var classExist = false;
            for(var classKey = 0; classKey < classesArray.length; classKey ++){
              if(\'class="\' + classesArray[classKey] + \'"\' == matches2[i2]){
                classExist = true;
              }
            }

            if(!classExist){
              tmpContent = tmpContent.replace(matches2[i2], "");
            }
          }
        }


        /* remove unknown inline styles */
        var styles = new Array("text-align: right;", "text-align: left;", "text-align: justify;");
        var pattern = /<[^<>]+style="[^"]+"[^<>]*>/gi; /* find all tags containing inline styles */
        var matches = tmpContent.match(pattern);
        for(var i =0; matches && i < matches.length; i++){ /* loop through found tags */
          var pattern2 = /style="[^"]+"/gi;  /* find style */
          var matches2 = matches[i].match(pattern2);
          for(var i2 = 0; matches2 && i2 < matches2.length; i2++){ /* throw away unknown inline styles */
            var styleExist = false;
            for(var styleKey = 0; styleKey < styles.length; styleKey ++){
              if(\'style="\' + styles[styleKey] + \'"\' == matches2[i2]){
                styleExist = true;
              }
            }

            if(!styleExist){
              tmpContent = tmpContent.replace(matches2[i2], ""); 
            }
          }
        }

        o.content = tmpContent;

      },
      paste_postprocess : function(pl, o) {
      }

    });
';


  }
}


