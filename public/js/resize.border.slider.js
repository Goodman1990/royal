function checkSkrin() {

    cuurentScreen  = document.body.clientWidth;

    if(cuurentScreen<=1200){

        var	height = (385-(1200-cuurentScreen)/3.12);//619
        $('.border-slyder').css( "height", height+"px");

    }else{

        $('.border-slyder').css( "height", "385px" );

    }
    if(cuurentScreen<=1200){

        var	height = (153-(1200-cuurentScreen)/10.12);//619
        $('.uzor-topr').css( "height", height+"px");

    }else{

        $('.uzor-top').css( "height", "153px" );

    } if(cuurentScreen<=1200){

        var	height = (139-(1200-cuurentScreen)/10.12);//619
        $('.warpper-uzor-top').css( "height", height+"px");

    }else{

        $('.warpper-uzor-top').css( "height", "139" );

    }
    setTimeout(function(){
        checkSkrin();
    }, 500)

}
checkSkrin();
