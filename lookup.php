<?php
$mydicts=array();
$mydicts['ox']='牛津英汉双解美化版';
$mydicts['ld']='朗道英汉字典5.0';
$mydicts['21']='21世纪双语科技词典';
$input=$_GET["word"];

$dictids=$_GET["dicts"];

if(is_null($dictids)){
    exit;
}

$dictids=explode(',',$dictids);

$output_stack=array();


foreach($dictids as $dictid){
    $dict=$mydicts[$dictid];
    if(is_null($dict)){
        continue;
    }

    $result=null;
    
    exec("LANG=\"en_US.UTF8\" /usr/local/bin/sdcv -n --utf8-output -u \"".$dict."\" ".$input,$result);

    if(sizeof($result)<3) continue;

    array_push($output_stack,"<div class=\"cdict\">[$dict]</div>");
    array_push($output_stack,"<hr/>");

    $result=array_slice($result,1);
    
    $word_state=0;
    $current_word="";

    foreach($result as $line){
        if(($word_state==0 or $word_state==2) and strcmp(substr($line,0,3),"-->")==0){
            $word_state=1;
            continue;
        }elseif($word_state==1 and strcmp(substr($line,0,3), "-->")==0){
            $current_word=substr($line,3);
            $word_state=2;
            array_push($output_stack,"<div class='word'>$current_word</div>");
            continue;
        }
        if(strlen($line) == 0){
            continue;
        }
        $tmp=htmlspecialchars($line, ENT_QUOTES);
        $tmp=preg_replace("/([ \*\";])(".$input."|".$current_word.")([\.,\" ;\-$])/i",'$1<b>$2</b>$3',$tmp);

        array_push($output_stack,"<div class=\"item\">$tmp</div>");
    }
    array_push($output_stack,"<br/>");

}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
        Remove this if you use the .htaccess -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title><?php echo $input ?></title>
        <meta name="description" content="" />
        <meta name="author" content="Young" />
        <meta name="viewport" content="width=device-width; initial-scale=1.0" />
        <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
        <link rel="shortcut icon" href="/favicon.ico" />
        <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
        <style type="text/css" media=screen>
            b  {color:#ff9900;}
            div.word  {line-height:2;font-size:large;font-weight:bold;}
            div.item {line-height:1.1;}
            div.item {font-size:small;}
            div.cdict {font-size:small; color:#66ccff; font-style:italic;}
            hr {line-height:0.5;}
        </style>
    </head>
    <body>
        <div>
            <header>
                <h3><?php echo $input ?></h3>
            </header>
            <div>
                <?php
                    foreach($output_stack as $line){
                        echo $line;
                    }
                ?>
            </div>
        </div>
    </body>
</html>
