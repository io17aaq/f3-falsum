<?php

class ErrorHandler
{
    public static function instance($f3)
    {
        $f3->set('ONERROR',
            function ($f3) {
                $status = $f3->get('ERROR.status');
                $code = $f3->get('ERROR.code');
                $text = $f3->get('ERROR.text');
                $traces = $f3->get('ERROR.trace');

                $errors = [];
                $i = 0;
                preg_match_all("/\[.*:\d+\]/", strip_tags($traces), $matches);
                foreach ($matches[0] as $result) {
                    $result = str_replace(['[', ']'], '', $result);
                    preg_match_all("/:\d+/", $result, $line);
                    $errors[$i]['line'] = str_replace(':', '', $line[0][0]);
                    $errors[$i]['file'] = str_replace(':'.$errors[$i]['line'], '', $result);
                    if ($i == 0) {
                        $eol = '';
                        $line = $errors[$i]['line'] - 1;
                        $line_start = $line - 6;
                        $line_end = $line + 6;
                        $pos = 0;
                        $rows = file($errors[$i]['file']);
                        $errors[$i]['script'] = '<div class="code-wrap">';
                        $errors[$i]['script'] .= '<p>File: <span class="file-link">'.$errors[$i]['file'].' </span>'.$errors[$i]['line'].'</p>';
                        $errors[$i]['script'] .= '<pre class="excerpt">'.$eol;
                        for ($pos = $line_start; $pos <= $line_end; $pos++):
                            $row = isset($rows[$pos]) ? $rows[$pos] : '';
                        if ($pos == $line):
                                $errors[$i]['script'] .= '<code class="error-line">'.$pos.' '.htmlentities($row).'</code>'.$eol; else:
                                $errors[$i]['script'] .= '<code>'.$pos.' '.htmlentities($row).'</code>'.$eol;
                        endif;
                        endfor;
                        $errors[$i]['script'] .= '</pre></div>';
                    } else {
                        if ($pos == 1):
                            $html = '<h3>Call Stack</h3>';
                        endif;
                        $html .= '<div class="code-wrap">';
                        $html .= '<p>File: <span class="file-link">'.$errors[$i]['file'].'</span> '.$errors[$i]['line'].'</p>';
                        $html .= '</div>';
                    };
                    $i++;
                }

                $headers = '';
                $header = $f3->hive()['HEADERS'];
                foreach ($header as $key => $value) {
                    $headers .= '<p style="word-wrap: break-word;"><span>'.$key.'</span> '.$value.'</p>';
                }

                echo $f3->hive['AJAX'] ?
                    json_encode($f3->hive['ERROR']) :
                    ('<!DOCTYPE html>
					<html>
					<head>
						<title>'.$code.' '.$status.'</title>
						<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.4/highlight.min.js"></script>
						<script>hljs.initHighlightingOnLoad();</script>
						<style>
							html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,embed,figure,figcaption,footer,header,hgroup,menu,nav,output,ruby,section,summary,time,mark,audio,video{margin:0;padding:0;border:0;font-size:100%;font:inherit;vertical-align:baseline}article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block}body{line-height:1}ol,ul{list-style:none}blockquote,q{quotes:none}blockquote:before,blockquote:after,q:before,q:after{content:"";content:none}table{border-collapse:collapse;border-spacing:0}body{font-family:"Source Sans Pro",sans-serif!important;background-color:#FFF!important;color:#222!important;font-size:14px!important}.grid{width:97%!important;margin:0 auto!important}#header{background-color:#2C3E50!important;color:#f4f4f4!important;padding:30px 0!important}#header h1{font-size:30px!important;font-weight:400!important;text-shadow:0 1px #000!important;padding-bottom:10px!important}#header h2{font-size:16px!important;font-weight:400!important;padding-top:10px!important}h3{font-size:18px!important;color:#3298E5!important;padding-top:30px!important}.code-wrap{background-color:#F4F3F1!important;border-radius:5px!important;width:100%!important;margin:10px 0!important;padding:5px!important}.code-wrap p{font-weight:700!important;margin:5px!important}.code-wrap span.file-link{font-weight:400!important;border-bottom:1px dotted #aaa!important}.code-wrap .excerpt{border:1px dotted #aaa!important;background-color:#FDF5CE!important;margin:10px 0!important;padding:10px!important}code{font-family:monospace!important;font-size:12px!important;padding:2px 8px!important;margin:3px 0!important}code.error-line{background-color:#D00202!important;color:#FFF!important}.headers{padding-top:10px!important}.headers p{font-family:monospace!important;color:#999!important;font-size:14px!important;line-height:22px!important}.headers p span{text-transform:uppercase!important;color:#463C54!important;width:260px!important;display:inline-block!important}.hljs{display:block!important;overflow-x:auto!important;padding:2px 5px!important;background-color:#FDF5CE!important;color:#D00202!important;-webkit-text-size-adjust:none!important}.hljs-variable{color:#393939!important}.hljs-string,.hljs-tag .hljs-value,.hljs-filter .hljs-argument,.hljs-addition,.hljs-change,.apache .hljs-tag,.apache .hljs-cbracket,.nginx .hljs-built_in,.tex .hljs-formula{color:#393939!important}.hljs-comment,.hljs-shebang,.hljs-doctype,.hljs-pi,.hljs-javadoc,.hljs-deletion,.apache .hljs-sqbracket{color:#565656!important}.hljs-keyword,.hljs-tag .hljs-title,.ini .hljs-title,.lisp .hljs-title,.http .hljs-title,.nginx .hljs-title,.css .hljs-tag,.hljs-winutils,.hljs-flow,.apache .hljs-tag,.tex .hljs-command,.hljs-request,.hljs-status{font-weight:700!important;color:#D00202!important}.error-line .hljs,.error-line .hljs-variable,.error-line .hljs-string,.error-line .hljs-tag
							.error-line .hljs-value,.error-line .hljs-filter
							.error-line .hljs-argument,.error-line .hljs-addition,.error-line .hljs-change,.error-line .apache .hljs-tag,.error-line .apache .hljs-cbracket,.error-line .nginx .hljs-built_in,.error-line .tex .hljs-formula,.error-line .hljs-comment,.error-line .hljs-shebang,.error-line .hljs-doctype,.error-line .hljs-pi,.error-line .hljs-javadoc,.error-line .hljs-deletion,.error-line .apache .hljs-sqbracket.hljs-keyword,.error-line .hljs-tag .hljs-title,.error-line .ini .hljs-title,.error-line .lisp .hljs-title,.error-line .http .hljs-title,.error-line .nginx .hljs-title,.error-line .css .hljs-tag,.error-line .hljs-winutils,.error-line .hljs-flow,.error-line .apache .hljs-tag,.error-line .tex .hljs-command,.error-line .hljs-request,.error-line .hljs-status{font-weight:700!important;color:#FFF!important}.go-back{text-decoration:none;padding-right:10px;color:#eee}
						</style>
					</head>
					<body>
						<div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto; background-color: #FFF; padding-bottom: 40px; z-index: 9999;">
							<div id="header">
								<div class="grid">
									<h1 style="color: #FFF">'.$status.' '.$code.'</h1>
									<h2 style="color: #FFF"><a href="javascript:;" class="go-back" onclick="history.go(-1);">&#8592;</a>  '.$text.'</h2>
								</div>
							</div>
							<div class="source grid">
								<h3>Source Code</h3>
								'.$errors[0]['script'].'
								'.$html.'
								<h3>Headers</h3>
								<div class="headers">'.$headers.'</div>
							</div>
						</div>
					</body>
					</html>');
            }
        );
    }
}
