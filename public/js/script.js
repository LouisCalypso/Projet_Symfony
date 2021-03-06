$(document).ready(function(){

    renderFigures();

    /**
     * triggered when clic on a vote button
     * get post id
     * call voteAction in HomeController
     * success: update vote number
     */
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

    /**
     * triggered when clic on Top or Newest button (sort buttons)
     * get posts list diplay preferences
     * call updateAction in HomeController
     * success: render a new post-list
     */
    //On écoute le clic sur les boutons newest et top
    $(document).on('click',".sort-trigger", function () {
        var self = $(this);
        var postsPerPage = $('#posts-per-page option:selected').val();
        var type = self.data('category');
        var page = self.data('page');
        console.log(page);
        console.log('posts', postsPerPage, page);

        self.children(".content").replaceWith(getSpinner("primary"));

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
                $('.posts-list').replaceWith(data);
                renderFigures();
            }
        })

    });

    /**
     * triggered when number posts per page preference is changed
     * get posts list diplay preferences
     * call updateAction in HomeController
     * success: render a new post-list
     */
    $(document).on('change',"#posts-per-page",function(){
        var self = $(this);
        var postsPerPage = $('#posts-per-page option:selected').val();
        var type = self.data('category');
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
                $('.posts-list').replaceWith(data);
                renderFigures();
            }
        })
    })




    //On écoute le "click" sur le bouton ayant la classe "modal-trigger"
    $(document).on('click','.modal-trigger',function () {
        //On récupère l'url depuis la propriété "Data-target" de la balise html a
        url = $(this).attr('data-target');

        //On initialise les modales materialize
        $(".modal").modal();
        $('.modal-content').html(getSpinner("primary"));

        //on fait un appel ajax vers l'action symfony qui nous renvoie la vue
        $.get(url, function (data) {
            //on injecte le html dans la modale
            $('.modal-content').html(data);
            //on ouvre la modale
            //$(modal).modal('show');
        });
    });

    /**
     * buttons display management
     */
    $(".btn-panel").hover(
        function() { $(this).children(".btn-panel-collapse").collapse('show'); },
        function() { $(this).children(".btn-panel-collapse").collapse('hide'); }
    );

    $('[data-toggle="tooltip"]').tooltip();
    $('.delete-post').parents('.post').css("transition","ease .5s");
    $('.delete-post').hover(
        function() {  $(this).parents('.post').toggleClass(['border-danger', 'shadow']); },
        function() {  $(this).parents('.post').toggleClass(['border-danger', 'shadow']); }
    );


    /**
     * Delete post
     * get post id and call deleteAction in PostController
     * success : remove selected post
     */
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

const renderFigures = () => {
    $("figure img").each((k, e) => {
        if ($(e).height() < $(e).width())
            $(e).css({
                height: "100%",
                left: "50%",
                transform: "translateX(-50%)",
            });
        else $(e).css({
            width: "100%",
            top: "50%",
            transform: "translatey(-50%)",
        });
    });
}

const getSpinner = color => {
    return $('<div>').addClass(["w-100", "text-center"]).html(
        $('<div>')
            .addClass(["spinner-border", "text-" + color])
            .css({width: "3erm", height: "3erm"})
            .attr("role","status")
            .html($("<span>").addClass("sr-only").text("Loading..."))
    );
}