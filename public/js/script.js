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
                self.parent().children("span").html(data.nbVote);
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


});