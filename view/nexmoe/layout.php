
<?php 

$var=explode("/",$_SERVER["REQUEST_URI"]);
$驱动器=$var["1"];

?>

<!DOCTYPE html>
<html>
<head>
	    <meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no"/>
    	<title><?php e($title.' - '.config('site_name'));?></title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/mdui@0.4.1/dist/css/mdui.min.css" >
    	<script src="https://cdn.jsdelivr.net/npm/mdui@0.4.1/dist/js/mdui.min.js" ></script>
    	<script src="//cdn.bootcss.com/jquery/1.12.3/jquery.min.js"></script>	
    	<script src="//cdn.staticfile.org/layer/2.3/layer.js"></script>
    	<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
    	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aplayer/dist/APlayer.min.css">
    	<link rel="shortcut icon" href="https://i.shangc.net/article/20190312/1a30c7effb168cbdb794098c476be875.jpg">

	 <style>
		.mdui-appbar .mdui-toolbar{
			height:64px;
			font-size: 15px;
		}
       	.mdui-toolbar>*{
			padding: 0 6px;
			margin: 0 2px;
			opacity:0.5;
		}
		.mdui-toolbar>.mdui-typo-headline{
			padding: 0 1px 0 0;
		}
		.mdui-toolbar>i{
			padding: 0;
		}
		.mdui-toolbar>a:hover,a.mdui-typo-headline,a.active{
			opacity:1;
		}
		.mdui-container{
			max-width:950px;
		}
		.mdui-list-item{
			-webkit-transition:none;
			transition:none;
		}
		.mdui-list>.th{
			background-color:initial;
		}
		.mdui-list-item>a{
			width:100%;
			line-height: 48px
		}
		.mdui-list-item{
			margin: 2px 0px;
			padding:0;
		}
		.mdui-toolbar>a:last-child{
			opacity:1;
		}
		#instantclick-bar {
        		background: white;
        	}
		.mdui-video-fluid {
            height: -webkit-fill-available;
        }
		.dplayer-video-wrap .dplayer-video {
			height: -webkit-fill-available !important;
		}
        .gslide iframe, .gslide video {
            height: -webkit-fill-available;
        }
		@media screen and (max-width:950px)	{		
			.mdui-list-item .mdui-text-right{
				display: none;
			}
			.mdui-container{
				width:100% !important;
				margin:0px;
			}
			.mdui-toolbar>*{				
				display: none;
			}
			.mdui-toolbar>a:last-child,.mdui-toolbar>a:nth-last-of-type(2),.mdui-toolbar>.mdui-typo-headline,.mdui-toolbar>i:first-child,.mdui-toolbar-spacer{
				display: block;
			}
		}
		.spec-col{padding:.9em;display:flex;align-items:center;white-space:nowrap;flex:1 50%;min-width:225px}
		.spec-type{font-size:1.35em}
		.spec-value{font-size:1.25em}
		.spec-text{float:left}
		.device-section{padding-top:30px}
		.spec-device-img{height:auto;height:340px;padding-bottom:30px}
		#dl-header{margin:0}
		#dl-section{padding-top:10px}
		#dl-latest{position:relative;top:50%;transform:translateY(-50%)}
	</style>
</head>
<body class=" mdui-appbar-with-toolbar mdui-theme-primary-indigo mdui-theme-accent-pink   ">
	<header class="mdui-appbar mdui-appbar-fixed mdui-color-theme mdui-appbar-inset">	
	<div class="mdui-toolbar mdui-color-theme">
        <span class="mdui-btn  mdui-typo-headline mdui-btn-icon mdui-ripple mdui-ripple-white" mdui-drawer="{target: '#main-drawer', swipe: true}" mdui-tooltip="{content: '菜单'}"><i class="mdui-icon material-icons">menu</i></span>
		<a href="/" class="mdui-typo-headline"><?php e(config('site_name'));?></a>
		<?php foreach((array)$navs as $n=>$l):?>
			<i class="mdui-icon material-icons mdui-icon-dark" style="margin:0;">chevron_right</i>
			<a href="<?php e("/".$驱动器.$l);?>"><?php e($n);?></a>
		<?php endforeach;?>
		<div class="mdui-toolbar-spacer"></div>
		<a href="javascript:thumb();" id="thumb" class="mdui-btn mdui-btn-icon" mdui-tooltip="{content: '切换显示'}"><i class="mdui-icon material-icons">format_list_bulleted</i></a>
	</div>	
	</header>

<div class="mdui-drawer mdui-drawer-close mdui-color-indigo-50" id="main-drawer">
	<div class="mdui-grid-tile">
		<a href="javascript:;"><img src="https://i.shangc.net/article/20190312/1a30c7effb168cbdb794098c476be875.jpg"/></a>
		<div class="mdui-grid-tile-actions mdui-grid-tile-actions-gradient">
			<div class="mdui-grid-tile-text">
				<div class="mdui-grid-tile-title"><?php e($title.' - '.config('site_name'));?></div>
				<div class="mdui-grid-tile-subtitle">Onedrive</div>
    			</div>
   		 </div>
	</div>   
	<div class="mdui-list" mdui-collapse="{accordion: true}">
	    
	    
		<a href="/" class="mdui-list-item mdui-ripple">
			<i class="mdui-list-item-icon mdui-icon material-icons">home</i>
			<div class="mdui-list-item-content">首页</div>
		</a>
		
		
		
		<?php 
		
		
$filess = scandir(ROOT."config/");
//显示

count($filess);
unset($filess[0]);
unset($filess[1]);
unset($filess[2]);
$meme=$filess;
foreach ($meme as $v)
{
    
    $v=str_replace(".php","",$v);
    
    echo '
    <a href="/'.$v.'" class="mdui-list-item mdui-ripple">
			<i class="mdui-list-item-icon mdui-icon material-icons">home</i>
			<div class="mdui-list-item-content">'.$v.'</div>
		</a>
    ';
    


}
	
	if($_COOKIE["admin"]==config("password@base"))	
	echo'
	  
		<a href="/install.php" class="mdui-list-item mdui-ripple">
			<i class="mdui-list-item-icon mdui-icon material-icons">home</i>
			<div class="mdui-list-item-content">添加新盘</div>
		</a>
	
	';
		
		
		?>
		
		
		
		
		
		
		
    		<?php e(config('drawer'));?>
	</div>
</div>
  
 
<div class="mdui-container">
	<div class="mdui-container-fluid"></div>
    	<?php view::section('content');?>
</div>
</body>
</html>
