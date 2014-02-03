/**
 * Created by goodman on 03.02.14.
 */
crop = {
    selfCrop:'',
    optionsCrop : {
        aspectRatio:'',
        minHeight : '',
        minWidth : '',
        x1:'',
        y1:'',
        x2:'',
        y2:'',
        onInit:'',
        onSelectStart:'',
        onSelectChange:'',
        onSelectEnd:''
    },
    settings:{
        target:'',
        modelWindow:'',
        preview:''

    },

    setDefault:function(){

        selfCro.optionsCrop.aspectRatio = '1:1';
        selfCro.optionsCrop.minHeight = '50';
        selfCro.optionsCrop.minWidth = '50';
        selfCro.optionsCrop.x1 = '0';
        selfCro.optionsCrop.y1 = '0';
        selfCro.optionsCrop.x2 = '50';
        selfCro.optionsCrop.y2 = '50';
        selfCro.settings.target = '#cropImage';
        selfCro.settings.preview= '#cropPreview';
        selfCro.settings.modelWindow= true;
    },

    init:function(lib){
        libCrop = lib;
        selfCrop = this;
    },
    setOptions:function (dataSelectors) {
        for(var key in dataSelectors){
            this.selectors[key] = dataSelectors[key]
        }
    },

    crop:function(){

        $(settings.target).imgAreaSelect({optionsCrop});

    }

//$('#target').imgAreaSelect({
//    x1: 0, y1: 0, x2: 50, y2: 50,
//    aspectRatio: '1:2',
//    handles: true,
//    zIndex: 1000000,
//    minHeight: 50,
//    minWidth: 50,
//    onInit: function(img, selection) {
//
//        $("#x1").val(0);
//        $("#y1").val(0);
//        $("#x2").val(50);
//        $("#y2").val(50);
//        $("#w").val(50);
//        $("#h").val(50);
//    },
//    onSelectEnd: function(img, selection) {
//
//        $("#x1").val(selection.x1);
//        $("#y1").val(selection.y1);
//        $("#x2").val(selection.x2);
//        $("#y2").val(selection.y2);
//        $("#w").val(selection.width);
//        $("#h").val(selection.height);
//
//    }
//
//
//
//});
};