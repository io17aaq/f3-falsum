<?php

namespace Falsum;

class Run
{

	public static function handler($override = false)
	{
		/**
		 * Create a framework instance variable.
		 */

		$fwi = \Base::instance();

		if($override){
			self::showErrors($fwi);
		}else{
			if($fwi->get('DEBUG') == 3){
				self::showErrors($fwi);
			}
		}
	}

	public static function showErrors($fwi)
	{	
		/**
		 * Set the ONERROR property.
		 */
		$fwi->set('ONERROR',
			function($fwi) {
				$resources = __DIR__ . '/Resources/';

				/**
				 * CSS Files
				 */
				$railscast = file_get_contents($resources.'css/railscast.css');
				$styles = file_get_contents($resources.'css/style.css');

				/**
				 * JS Files
				 */
				$jquery = file_get_contents($resources.'js/jquery.js');
				$main = file_get_contents($resources.'js/main.js');

				$status = $fwi->get('ERROR.status');
				$code = $fwi->get('ERROR.code');
				$text = $fwi->get('ERROR.text');
				$trace = $fwi->get('ERROR.trace');

				preg_match_all("/\[.*:\d+\]/", strip_tags($trace), $matches);
				foreach ($matches[0] as $key => $result) {
				    $result = str_replace(['[', ']'], '', $result);
				    preg_match_all("/:\d+/", $result, $line);
				    $errors[$key]['line'] = str_replace(':', '', $line[0][0]);
				    $errors[$key]['file'] = str_replace(':'.$errors[$key]['line'], '', $result);
				    
				    $eol = '';
				    $line = $errors[$key]['line'] - 1;
				    $line_start = $line - 6;
				    $line_end = $line + 6;
				    $pos = 0;
				    $rows = file($errors[$key]['file']);
				    $errors[$key]['script'] = '<div class="code-wrap">';
				    $errors[$key]['script'] .= '<pre class="excerpt">'.$eol;
				    for($pos = $line_start; $pos <= $line_end; $pos++):
				        $row = isset($rows[$pos]) ? $rows[$pos] : '';
					    if ($pos == $line):
					        $errors[$key]['script'] .= '<code class="error-line">'.$pos.' '.htmlentities($row).'</code>'.$eol; else:
					        $errors[$key]['script'] .= '<code>'.$pos.' '.htmlentities($row).'</code>'.$eol;
					    endif;
				    endfor;
				    $errors[$key]['script'] .= '</pre></div>';
				    $key++;
				}

				$html_structure = ''.
				'<html>' .
				'	<head>' .
				'		<style>' . $styles . '</style>' .
				'		<style>' . $railscast . '</style>' .
				'		<script type="text/javascript">' . $jquery . '</script>' .
				'		<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.1.0/highlight.min.js"></script>' .
				'	</head>' .
				'	<body>' .
				'		<div id="container">' .
				'			<div class="header">' .
				'				<h1>'.$code.' '.$status.'</h1>' .
				'				<h2>'.$text.'</h2>' .
				'			</div>' .
				'			<div class="content">' .
				'				<div class="left"><div>' .
				'					<h3>Code Analysis</h3>';

				foreach($errors as $key => $error){
					$selected = $key == 0 ? ' selected' : '';
					$html_structure .= ''.
					'<div class="code'.$selected.'" ref="'.$key.'">'.$error['script'].'</div>';
				}

				$html_structure .= '<h3 class="headers">Headers</h3>' ;

				foreach($fwi->get('HEADERS') as $key => $value){
					$html_structure .= '<div class="variables"><span>'.$key.'</span> '.$value.'</div>';
				}

				$html_structure .= ''.
				'				</div></div>' .
				'				<div class="right"><div>' .
				'					<h3>Error Stack</h3><div class="stacks">';

				foreach($errors as $key => $error){
					$selected = $key == 0 ? ' selected' : '';
					$path = substr($error['file'], -25);
					$html_structure .= ''.
					'<div class="stack'.$selected.'" ref="'.$key.'">' .
					'	<h4><span class="pos">'.$key.'</span> Line Number '.$error['line'].'</h4>' .
					'	<p>...'.$path.'</p>' .
					'</div>';
				}

				$html_structure .= ''.
				'				</div></div></div>' .
				'			</div>' .
				'		</div>' .
				'		<script type="text/javascript">' . $main . '</script>' .
				'	</body>' .
				'</html>';

				echo $html_structure;
			}
		);
	}
}
