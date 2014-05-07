<?php
/*
Plugin Name: Holy Quran
Plugin URI: http://webgalli.com/
Description: Allows you to display random Quran Ayahs along with translation and MP3 Sound track.
Version: 1.3
Author: Mohammed Aqeel
Author URI: http://www.webgalli.com/blog/random-specific-daily-quran-ayah-plugins-for-elgg-and-wordpress/
*/

/*  Copyright 2012 Team Webgalli - http://www.webgalli.com/blog/random-specific-daily-quran-ayah-plugins-for-elgg-and-wordpress/

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function get_Quran_verse($specific , $translation, $audio) {
	$pluginspath = dirname( __FILE__ );
	$player = get_bloginfo('wpurl') . "/wp-content/plugins/holy-quran-random-ayahs/audioplayer/";
	$directory = $pluginspath."/lib/";  
	$translation_directory = $pluginspath."/lib/translations/"; 
	if (!$specific){
		$num = rand(1,6236);
	} else {
		$num = (int) $specific;
	}
	$num = $num - 1;	
	if (!$translation){
		$translation = 'en.ahmedali';
	}
	$quotefile = "quran-simple.txt";
	$translation_quotefile = $translation.".txt";
	$quotes = file($directory.$quotefile);
	$translation_quotes = file($translation_directory.$translation_quotefile);
	$quote = $quotes[$num];
	$translation_quote = $translation_quotes[$num];
	$explode_quote = explode("|",$quote);
	$surah = str_pad((int) $explode_quote[0],3,"0",STR_PAD_LEFT);
	$ayah = str_pad((int) $explode_quote[1],3,"0",STR_PAD_LEFT);
	$verse = "<div id='holyQuran_arabic'>".$explode_quote[2]."</div>";
	$translation_verse = "<div id='holyQuran_translation'>".$translation_quote."</div>";
	$line_break = "</br>";
	$html = $verse.$translation_verse.$line_break;
	if ($audio){
		$html .= "<script type='text/javascript' src='{$player}audio-player.js'></script>  
			<script type='text/javascript'>  
			AudioPlayer.setup('{$player}/player.swf', {  
			width: 220  
			});  
		</script>";  
		$mp3 = "http://www.everyayah.com/data/Menshawi_32kbps/".$surah.$ayah.".mp3";
		$html .= "<div style='margin:5px 0 10px 5px;'>
		<p id='audioplayer_1'>Alternative content</p>  
         <script type='text/javascript'>  
         AudioPlayer.embed('audioplayer_1', {soundFile: '$mp3'});  
         </script> 
		</div>"; 
	}
	echo $html ;
}

function get_installed_Quran_translations(){
	$pluginspath = dirname( __FILE__ );
	$translation_directory = $pluginspath."/lib/translations/"; 
    $path = realpath($translation_directory); 
	$translations = array();
	if($dir_handle = @opendir($path)){
		while (false !== ($file = readdir($dir_handle)))
		{
			if ($file != "." && $file != ".." && strtolower(substr($file, strrpos($file, '.') + 1)) == 'txt')
			{
				$filename = explode('.txt',$file);
				$translations[] = $filename[0];
			}
		}
		closedir($dir_handle);
	}	
	return $translations;
}

function holy_Quran_control()
{
  $options[title] = get_option("widget_holy_Quran_title");
  $options[language] = get_option("widget_holy_Quran_language");
  $options[audio] = get_option("widget_holy_Quran_audio");
  if (!is_array( $options )){
	$options = array(
      'title' => 'Holy Quran Title',
      'language' => 'en.ahmedali',
      'audio' => 'true'
      );
  }
  if ($_POST['holy_Quran-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['widget_holy_Quran_title']);
    $options['language'] = htmlspecialchars($_POST['widget_holy_Quran_language']);
    $options['audio'] = htmlspecialchars($_POST['widget_holy_Quran_audio']);
    update_option("widget_holy_Quran_title", $options['title']);
    update_option("widget_holy_Quran_language", $options['language']);
    update_option("widget_holy_Quran_audio", $options['audio']);
   }
	$translations = get_installed_Quran_translations();
?>
  <p>
    <label for="holy_QuranWorld-WidgetTitle">Widget Title: </label>
    <input type="text" id="widget_holy_Quran_title" name="widget_holy_Quran_title" value="<?php echo $options['title'];?>" /><br/>
    <label for="widget_holy_Quran_language">Default translation:</label>
	<select name="widget_holy_Quran_language">
	<?php 
		foreach ($translations as $translation) { 
			if ($options[language] == $translation) {
				$selected = "selected='yes'";
				}else {
				$selected = "";
				}
			echo "<option $selected value='{$translation}' >{$translation}</option>";
		} 
	?>
	</select><br/>
    <label for="widget_holy_Quran_audio">Show audio:</label>
	<select name="widget_holy_Quran_audio">
	<?php
	?>
	<option <?php if ($options[audio] == 'true') { echo "selected='yes'";} ?> value ="true">Yes</option>
	<option <?php if ($options[audio] == 'false') { echo "selected='yes'";} ?> value ="false">No</option>
	</select>
    <input type="hidden" id="holy_Quran-Submit" name="holy_Quran-Submit" value="1" />
  </p>
<?php
}

function widget_holy_Quran($args) {
  extract($args);
  $options['title'] = get_option("widget_holy_Quran_title");
  $options['translation'] = get_option("widget_holy_Quran_language");
  $options['audio'] = get_option("widget_holy_Quran_audio");
  if (!is_array( $options )){
	$options = array(
      'title' => 'Holy Quran',
      'language' => 'en.ahmedali',
      'audio' => 'true'
    );
  }
	echo $before_widget;
    echo $before_title;
    echo $options['title'];
    echo $after_title;
	get_Quran_verse('',$options[translation],$options[audio]);
	echo $after_widget;
}

function init_Quran_widget() {
	register_sidebar_widget('Holy Quran', 'widget_holy_Quran');
	register_widget_control('Holy Quran', 'holy_Quran_control', 300, 200 );
}

add_action("plugins_loaded", "init_Quran_widget");

?>
