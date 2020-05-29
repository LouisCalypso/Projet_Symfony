$(document).ready(function(){

    $(".up-vote, .down-vote,.up-vote-toggled,.down-vote-toggled").click(function () {
        console.log("clic");
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

    //On écoute le "click" sur le bouton ayant la classe "modal-trigger"
    $(".newest-posts, .best-posts").click(function () {
        console.log("clic");
        var self = $(this);
        var type = self.attr('class');
        console.log(type);
        console.log(self.data('posts'))

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