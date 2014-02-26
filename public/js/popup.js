function PopUp(text, title, elementEvent, yesCallback, noCallback,buttonYesText,buttonNoText){
    elementEvent.preventDefault();
    var yesText = 'Да';
    var noText = 'Нет';
    //if(typeof )
    $('<div class="prompt">'+text+'</div>').dialog({
        dialogClass:'prompt-wrap',
        title: title,
        buttons:[
            {
            text:noText,
            class:'red',
            click:function(e){
                noCallback(elementEvent);
                $('.prompt').dialog('close');
            }},
            {
            text:yesText,
            class:'green',
            click:function(e){
                yesCallback(elementEvent);
                $('.prompt').dialog('close');
            }}],
        resizable:false,
        draggable:false,
        width:280,
        modal:true
    });
}

function SinglePopUp(text, title, elementEvent, yesCallback,withs){
    if(typeof withs =='undefined')withs = 280;
    successCallback = yesCallback || function(){};

    if(elementEvent)
        elementEvent.preventDefault();
    //if(typeof )
    $('<div class="prompt">'+text+'</div>').dialog({
        dialogClass:'prompt-wrap',
        title: title,
        buttons:[
            {
                text:'Да',
                class:'green except',
                click:function(event){
                    successCallback(event);
                    console.log('successCallback(event)', successCallback(event));
                    $('.prompt').dialog('close');
                }
            }
        ],
        resizable:false,
        draggable:false,
        width:withs,
        modal:true
    });
}
