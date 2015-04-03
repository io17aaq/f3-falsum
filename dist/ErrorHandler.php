<?php

class ErrorHandler{

	static function instance($f3){
		$f3->set('ONERROR',
		    function($f3) {
		      	$status = $f3->get('ERROR.status');
		      	$code = $f3->get('ERROR.code');
		      	$text = $f3->get('ERROR.text');
		      	$traces = $f3->get('ERROR.trace');
				
				$eol = "";
				$html = '';
				$pos = 0;
				foreach($traces as $trace):
					if($pos == 0):
						$line = $trace['line'] - 1;
						$line_start = $line - 6;
						$line_end = $line + 6;
						$rows = file($trace['file']);
						$html .= '<div class="code-wrap">';
						$html .= '<p>File: <span class="file-link">'.$trace['file'].' </span>'.$line.'</p>';
						$html .= '<pre class="excerpt">'.$eol;
						for($i = $line_start; $i <= $line_end; $i++):
							$row = isset($rows[$i]) ? $rows[$i] : '';
							if($i == $line):
								$html .= '<code class="error-line">'.$i.' '.$row.'</code>'.$eol;
							else:
								$html .= '<code>'.$i.' '.$row.'</code>'.$eol;
							endif;
						endfor;
						$html .= '</pre></div>';
					else:
						if($pos == 1)
							$html .= '<h3>Call Stack</h3>';
						$html .= '<div class="code-wrap">';
						$html .= '<p>File: <span class="file-link">'.$trace['file'].'</span> '.$trace['line'].'</p>';
						$html .= '</div>';
					endif;
					$pos++;
				endforeach;

				$headers = '';
				$header = $f3->hive()['HEADERS'];
				foreach($header as $key => $value):
					$headers .= '<p><span>'.$key.'</span> '.$value.'</p>'; 
				endforeach;

				$headers = '';
				$header = $f3->hive()['HEADERS'];
				foreach($header as $key => $value):
					$headers .= '<p><span>'.$key.'</span> '.$value.'</p>'; 
				endforeach;
				
				echo '
					<!DOCTYPE html>
					<html>
					<head>
						<title>ERROR '.$code.' - '.$status.'</title>
						<link rel="stylesheet" type="text/css" href="lib/error.css" >
						<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.4/highlight.min.js"></script>
						<script>hljs.initHighlightingOnLoad();</script>
					</head>
					<body>
						<div id="header">
							<div class="grid">
								<h1>'.$status.' '.$code.'</h1>
								<h2>'.$text.'</h2>
							</div>
						</div>
						<div class="source grid">
							<h3>Source Code</h3>
							'.$html.'
							<h3>Headers</h3>
							<div class="headers">'.$headers.'</div>
						</div>
					</body>
					</html>';
		    }
		);
	}

}