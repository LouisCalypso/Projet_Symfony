$(document).ready(function(){

    $(".up-vote, .down-vote,.up-vote-toggled,.down-vote-toggled").click(function () {
        console.log("clic");
        var self = $(this);
        var id = self.data("id");
        var type = self.attr('class');
        if(type == "up-vote-toggled" || type == "down-vote-toggled")
            return ;

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
                    self.parent().children(".up-vote").attr("class","up-vote-toggled");
                    self.parent().children(".down-vote-toggled").attr("class","down-vote");
                    self.parent().children(".down-vote").attr("src","/img/down-arrow.png");
                }else{
                    self.attr("src","/img/down-arrow-blue.png");
                    self.parent().children(".down-vote").attr("class","down-vote-toggled");
                    self.parent().children(".up-vote-toggled").attr("class","up-vote");
                    self.parent().children(".up-vote").attr("src","/img/up-arrow.png");
                }
            }
        })
    });

    //On écoute le "click" sur le bouton ayant la classe "modal-trigger"
    $('.modal-trigger').click(function () {
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
    $('[data-toggle="tooltip"]').parent().css("transition","ease .5s");
    $('[data-toggle="tooltip"]').hover(
        function() {  $(this).parent().toggleClass(['border-danger', 'border-white', 'shadow']); },
        function() {  $(this).parent().toggleClass(['border-danger', 'border-white', 'shadow']); }
    );

    $(".delete-post").click(function () {
        console.log("deleting post " + $(this).data("id"));
        var self = $(this);
        var id = self.data("id");

        $.ajax({
            type: "POST",
            url: '/posts/deleteAction/ajaxAction',
            dataType: "json",
            data: {
                "id": id
            },
            async: true,
            success: function(data) {
                console.log(data);
                self.parent().remove();
            }
        })
    });

});