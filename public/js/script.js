$(document).ready(function(){

    $(document).on('click',".up-vote, .down-vote,.up-vote-toggled,.down-vote-toggled",function () {
        var self = $(this);
        var id = self.data("id");

        switch(true) {
            case self.hasClass("up-vote") :
                var type = "up-vote";
                break;
            case self.hasClass("down-vote") :
                var type = "down-vote";
                break;
            default: return;
        }

        $.ajax({
            type: "POST",
            url: '/home/voteAction/ajaxAction',
            dataType: "json",
            data: {
                "id": id,
                "type": type
            },
            async: true,
            success: function(data) {
                self.parent().children(".nb-vote").html(data.nbVote);
                if(type == "up-vote"){
                    self.attr("src","/img/up-arrow-orange.png");
                    self.parent().children(".up-vote").toggleClass(["up-vote","up-vote-toggled"]);
                    self.parent().children(".down-vote-toggled").toggleClass(["down-vote-toggled","down-vote"]);
                    self.parent().children(".down-vote").attr("src","/img/down-arrow.png");
                }else{
                    self.attr("src","/img/down-arrow-blue.png");
                    self.parent().children(".down-vote").toggleClass(["down-vote","down-vote-toggled"]);
                    self.parent().children(".up-vote-toggled").toggleClass(["up-vote-toggled","up-vote"]);
                    self.parent().children(".up-vote").attr("src","/img/up-arrow.png");
                }
            }
        })
    });

    //On écoute le clic sur les boutons newest et top
    $(document).on('click',".sort-trigger", function () {
        var self = $(this);
        var postsPerPage = $('#articles-per-pages option:selected').val();
        var type = self.data('category');
        var page = self.data('page');
        console.log('posts', postsPerPage, page);

        $.ajax({
            type: "POST",
            url: '/home/updateAction/ajaxAction',
            dataType: "json",
            data: {
                "postsPerPage": postsPerPage,
                "type": type,
                "page": page
            },
            async: true,
            success: function(data) {
                console.log("SUCCESS");
                $('.posts-list').html(data);
            }
        })

    });

    $(document).on('change',"#articles-per-pages",function(){
        var self = $(this);
        var postsPerPage = $('#articles-per-pages option:selected').val();
        var type = 'best-posts';
        var page =  self.data('page');
        $.ajax({
            type: "POST",
            url: '/home/updateAction/ajaxAction',
            dataType: "json",
            data: {
                "postsPerPage": parseInt(postsPerPage),
                "type": type,
                "page": parseInt(page)


            },
            async: true,
            success: function(data) {
                console.log("SUCCESS");
                $('.posts-list').html(data);
            }
        })
    })



    //On écoute le "click" sur le bouton ayant la classe "modal-trigger"
    $(document).on('click','.modal-trigger',function () {
        //On récupère l'url depuis la propriété "Data-target" de la balise html a
        url = $(this).attr('data-target');

        //On initialise les modales materialize
        $(".modal").modal();
        $('.modal-content').html(
            '<div class="w-100 text-center">'
            + '<div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">'
            + '<span class="sr-only">Loading...</span>'
            + '</div>'
            + '</div>'
        );

        //on fait un appel ajax vers l'action symfony qui nous renvoie la vue
        $.get(url, function (data) {
            //on injecte le html dans la modale
            $('.modal-content').html(data);
            //on ouvre la modale
            //$(modal).modal('show');
        });
    });


    $(".btn-panel").hover(
        function() {  $(this).children(".btn-panel-collapse").collapse('show'); },
        function() { $(this).children(".btn-panel-collapse").collapse('hide'); }
    );

    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="tooltip"]').parents('.post').css("transition","ease .5s");
    $('[data-toggle="tooltip"]').hover(
        function() {  $(this).parents('.post').toggleClass(['border-danger', 'shadow']); },
        function() {  $(this).parents('.post').toggleClass(['border-danger', 'shadow']); }
    );

    $(".delete-post").click(function () {
        var self = $(this);
        var id = self.data("id");
        
        console.log("deleting post " + $(this).data("id"));
        $.ajax({
            type: "POST",
            url: '/posts/deleteAction/ajaxAction',
            dataType: "json",
            data: {
                "id": id
            },
            async: true,
            success: function(data) {
                if (self.parents('.post-list').children().length == 1)
                    self.parents('.container').remove();
                else self.parents('.post').remove();
            }
        })
    });

});