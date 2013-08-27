(function($)
{

    var namespace = $('table').data('namespace'),
        sort = $('select[name="sort"]').val(),
        search = $('input[name="search"]').val(),
        pageList = $("tbody#pageList"),
        template = Handlebars.templates['list'],

        showList = function(){
            var data = {
                "ajax" : true,
                "sort" : sort,
                "search" : search
            }
            
            $.ajax({
                type: 'POST',
                url: '/elements/content/item/list/' + namespace,
                data: data
            }).done(function(resp){
                //pageList.html(resp);
                pageList.html(template(resp));
            }).fail(function(){
                alert("showList is failing");
            });

            return false;
        },

        setActivated = function(){
                
            var curEl = $(this),
                pageId = curEl.closest('table').data('pageId'),
                data  = {
                    "pageId" : pageId
                };

            $.ajax({
              type: 'POST',
              url: '/elements/content/item/togglefeatured',
              data: data,
              dataType: 'json'
            }).done(function(resp){
                if(resp[0]){
                    curEl.addClass('btn-success').removeClass('btn-danger').data('activated', 'true').html('<i class="icon-ok icon-white"></i>');
                }else{
                    curEl.addClass('btn-danger').removeClass('btn-success').data('activated', 'false').html('<i class="icon-remove icon-white"></i>');
                }
            }).fail(function(){
                alert("setActivated is failing");
            });

            return false;

        },

        setPageStatus = function(){
            
            var curEl = $(this),
                status = curEl.val(),
                pageId = curEl.closest('table').data('pageId'),
                data  = {
                    "pageId" : pageId,
                    "status" : status
                };
            
            if (!confirm("Sure you want to change the Status?")) {
                $(this).val($.data(this, 'val')); //set back
            return;                           //abort!
            }

            //destroy branches
            $.data(this, 'val', status);
            
            
            $.ajax({
                type: 'POST',
                url: '/elements/content/item/setpagestatusfromlist',
                data: data,
                dataType: 'json'
            }).done(function(resp){
               alert("You have successfully changed your status.")
            }).fail(function(){
                alert("setPageStatus is not failing");
            });
            
            return false;
        },

        deletePage = function(event){
            
        },

        deleteBtn = $('.delete').on('click', deletePage),
        activeBtn = $('td').find('[data-activated]').on('click', setActivated),
        status = $('.pageStatus').on('change', setPageStatus),
        filterBtn = $("#filter").on('click', function(e) {
            e.preventDefault();
            showList();
        });

}(jQuery));









