<div class="landing" style="width: 100%; height: 100%; position: fixed; z-index: 9999; background: white;">


	<style>
	
	#loader{
		width:120px;
		height:240px;
		position: absolute;
		top: 45%;
		left: 50%;
	}
	
	#loader-bar{
		width:120px;
		height:auto;	
		display: block;
		-webkit-transform: rotate(0deg);
		
		-moz-animation-name:move_loader;
		-moz-animation-duration:1.5s;
		-moz-animation-iteration-count:infinite;
		-moz-animation-timing-function:linear;
		-webkit-animation-name:move_loader;
		-webkit-animation-duration:1.5s;
		-webkit-animation-iteration-count:infinite;
		-webkit-animation-timing-function:linear;
		-ms-animation-name:move_loader;
		-ms-animation-duration:1.5s;
		-ms-animation-iteration-count:infinite;
		-ms-animation-timing-function:linear;
		-o-animation-name:move_loader;
		-o-animation-duration:1.5s;
		-o-animation-iteration-count:infinite;
		-o-animation-timing-function:linear;
		animation-name:move_loader;
		animation-duration:1.5s;
		animation-iteration-count:infinite;
		animation-timing-function:linear;

	}
	
	
	@-webkit-keyframes move_loader{
	
		0%{
		-webkit-transform: rotate(0deg);
		}
		50%{
		-webkit-transform: rotate(180deg);
		}	
		100%{
		-webkit-transform: rotate(360deg);
		}	
		
	}
	
	@-moz-keyframes move_loader{
		0%{
		-webkit-transform: rotate(0deg);
		}
		50%{
		-webkit-transform: rotate(180deg);
		}	
		100%{
		-webkit-transform: rotate(360deg);
		}	
	}
	
	
	@-ms-keyframes move_loader{
		0%{
		-webkit-transform: rotate(0deg);
		}
		50%{
		-webkit-transform: rotate(180deg);
		}	
		100%{
		-webkit-transform: rotate(360deg);
		}	
	
	}
	
	@-o-keyframes move_loader{
		0%{
		-webkit-transform: rotate(0deg);
		}
		50%{
		-webkit-transform: rotate(180deg);
		}	
		100%{
		-webkit-transform: rotate(360deg);
		}	
	
	}
	
	@keyframes move_loader{
		0%{
		-webkit-transform: rotate(0deg);
		}
		50%{
		-webkit-transform: rotate(180deg);
		}	
		100%{
		-webkit-transform: rotate(360deg);
		}	
	}
	
	</style>
	
	<div id="loader">	
		<img id="loader-bar" class="loader" src="<?php bloginfo('template_directory'); ?>/_/img/mark.png" />
	</div>
						
</div>