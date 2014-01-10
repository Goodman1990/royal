function checkSkrin() {

    cuurentScreen  = document.body.clientWidth;

    if(cuurentScreen<=1200){

        var	height = (385-(1200-cuurentScreen)/3.12);//619
        $('.border-slyder').css( "height", height+"px");

    }else{

        $('.border-slyder').css( "height", "385px" );

    }
    setTimeout(function(){
        checkSkrin();
    }, 500)

}
checkSkrin();
