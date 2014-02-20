/**
 * Created with JetBrains PhpStorm.
 * User: artgen2
 * Date: 30.09.13
 * Time: 16:51
 * To change this template use File | Settings | File Templates.
 */
function isset () {

    var a=arguments, l=a.length, i=0;

    if (l==0) {
     return  false;
    }

    while (i!==l) {
        if (typeof(a[i])=='undefined' || a[i]===null) {
            return false;
        } else {
            i++;
        }
    }
    return true;
}



pagination = {

    //self:this,
    options : {


      limit:10,
      page:1,
      order:'desc',
      orderBy:'',
      search:'',
      dateRange:'',
      dateBegin:'',
      dateEnd:''

    },

    selectors:{

        innerDataSelector:'',
        searchSelector:'',
        dateRangeSelector:'',
        searchSubmitSelector:'',
        limitSelector:'',
        pageSelector:'',
        orderSelector:'',
        dateRangeSubmitSelector:''

    },

    sendPost:function(){

        $.post(
            document.location.href,this.options
        ).done(function(data) {
                $(selfPagination.selectors.innerDataSelector).html($(selfPagination.selectors.innerDataSelector,data).html());
                $(selfPagination.selectors.pageSelector).html($(selfPagination.selectors.pageSelector,data).html());
                if($('.no-record',data).length>0){
                    $(selfPagination.selectors.innerDataSelector).html('');
                }
         });


    },


    getOptions:function(){

        this.options.limit = $(this.selectors.limitSelector+' :selected').html();
        this.options.search =isset($(this.selectors.searchSelector).val())?$(this.selectors.searchSelector).val():"";
        this.options.order = this.getSort();
        this.options.orderBy = isset($('.'+this.options.order).attr('data-sort'))?$('.'+this.options.order).attr('data-sort'):"";
        this.options.dateBegin = isset($(this.selectors.dateRangeSelector).val())?$(this.selectors.dateRangeSelector).val().split('-')[0]:"";
        this.options.dateEnd =  isset($(this.selectors.dateRangeSelector).val())?$(this.selectors.dateRangeSelector).val().split('-')[1]:"";
        this.options.dateRange = isset($(this.selectors.dateRangeSelector).val())?$(this.selectors.dateRangeSelector).val():"";


    },



    showDesc:function (elem){
//        console.log(elem);
        $(elem).attr('class','desc');
        $('.desc').css({

            "background-image": 'url(/images/arrow4dawn.png)',
            "background-size": '14px',
            'background-repeat': 'no-repeat',
            'background-position': '90% center',
            'cursor':'pointer',
            "min-width": "10px",
            "min-hight": "10px"

        });

    },

    showAsc:function (elem){

        $(elem).attr('class','asc');
        $('.asc').css({

            "background-image": 'url(/images/arrow4top.png)',
            "background-size": '14px',
            'background-repeat': 'no-repeat',
            'background-position': '90% center',
            'cursor':'pointer',
            "min-width": "10px",
            "min-hight": "10px"
        });

    },

    getSort:function (){

    var sort = $('.desc').attr('data-sort');

    if(typeof (sort) == "undefined"){

        sort = 'asc'

    }else{

        sort = 'desc'

    }

    return sort;

},

    paginationEvent:function(){

        $(document).on('click',selfPagination.selectors.pageSelector+' a',function(e){

            e.preventDefault();
            selfPagination.options.page =  $(this).html();
            selfPagination.getOptions();
            if(selfPagination.options.page === 'позже'){
                selfPagination.options.page = parseInt($('.pages .current').html());
                selfPagination.options.page = selfPagination.options.page+1;
            }else if(selfPagination.options.page === 'раньше'){
                selfPagination.options.page = parseInt($('.pages .current').html());
                selfPagination.options.page = selfPagination.options.page-1;
            }
            selfPagination.sendPost()
        });


    },

    searchEvent:function(){

        $(document).on('click',selfPagination.selectors.searchSubmitSelector,function(e){

            e.preventDefault();

//
            selfPagination.getOptions();

            selfPagination.sendPost();


        });


    },

    dateRangeEvent:function(){

        $(document).on('click',selfPagination.selectors.dateRangeSubmitSelector,function(e){

            e.preventDefault();
//

            selfPagination.getOptions();

            selfPagination.sendPost()


        });


    },

    sortEvent:function(){


        $(document).on('click',selfPagination.selectors.orderSelector,function(e){

            e.preventDefault();
            var sort  =  $(this).attr('class');

            if(typeof (sort) == "undefined"){
                //console.log(this);
                $(selfPagination.selectors.orderSelector).removeAttr('class').css({
                    "background-image": 'none'
                });
                selfPagination.showDesc(this);

            } else if(sort === 'asc'){

                    $(selfPagination.selectors.orderSelector).removeAttr('class').css({
                        "background-image": 'none'
                    });

                    $(this).attr('class','desc');

                    selfPagination.showDesc(this);

            }else{

                    $(selfPagination).removeAttr('class').css({
                        "background-image": 'none'
                    });

                    selfPagination.showAsc(this);

                }

            selfPagination.getOptions();
            selfPagination.sendPost();

        });


    },
    limitEvent:function(){

        $(document).on('change',selfPagination.selectors.limitSelector,function(e){

            e.preventDefault();

            selfPagination.getOptions();

            selfPagination.sendPost();


        })

    },
    setSelectors:function (dataSelectors) {
        for(var key in dataSelectors){
            this.selectors[key] = dataSelectors[key]
        }
    },
    setDefaultSelectors:function(){
        selfPagination  = this;
        this.selectors.innerDataSelector='#history tbody';
        this.selectors.searchSelector='#search-input';
        this.selectors.dateRangeSelector='#date-range';
        this.selectors.searchSubmitSelector='#search-send';
        this.selectors.dateRangeSubmitSelector='#date-range-sande';
        this.selectors.limitSelector='#limit';
        this.selectors.pageSelector='.pager-links';
        this.selectors.orderSelector='thead th:not(.disabled)';

    },

    init:function(){

        selfPagination  = this;
        this.dateRangeEvent();
        this.sortEvent();
        this.searchEvent();
        this.paginationEvent();
        this.limitEvent();

    }




};
