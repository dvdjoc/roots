<?php
/**
 * LESS Compilation
 */
function RootsCompileLESS() {

    require_once('lessc.inc.php');
    
    $lessFiles = array(
    	array(
            'file' => 'main.less',
            'path_from' => get_template_directory() . '/assets/less/',
            'path_to' => get_template_directory() . '/assets/css/'
        ),      
    	// array(
     //        'file' => 'app.less',
     //        'path_from' => get_stylesheet_directory() . '/assets/less/',
     //        'path_to' => get_stylesheet_directory() . '/assets/css/',
     //    )
    );
    	    		    	
    foreach($lessFiles as $file) {

    	if( file_exists( $file['path_from'].$file['file'] ) ) // support child theme
    		RootsAutoCompileLESS( $file );        
    }
}
add_action('wp_head', 'RootsCompileLESS');

function RootsAutoCompileLESS($file) {
    
    // load the cache
    $cache_fname = $file['file'].".cache";
    $css_fname = str_replace('.less', '.css', $file['file']);

    if (file_exists($file['path_from'].$cache_fname)) {
    
    	$cache = unserialize(file_get_contents($file['path_from'].$cache_fname)); // squelch errors on unserialize as git often ruins oue serialized files

    	if( !file_exists($cache['root']) )  // dont use cache if cache file is from another install
    		$cache = $file['path_from'].$file['file'];
    } else {
        
    	$cache = $file['path_from'].$file['file'];
    }
    
    // compile less
    $lessc = new lessc;
    $new_cache = lessc::cexecute($cache, false, $lessc);

    if (!is_array($cache) || $new_cache['updated'] > $cache['updated']) {

    	file_put_contents($file['path_from'].$cache_fname, serialize($new_cache));
    	file_put_contents($file['path_to'].$css_fname, $new_cache['compiled']);
    }
}
