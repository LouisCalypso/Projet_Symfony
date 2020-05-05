$(document).ready(function(){
    $(".up-vote , .down-vote").click(function () {
        var id = $(this).data("id");
        var type = $(this).attr('class');
        console.log(id);
        var url = window.location.href + '/vote/' + type;
        var self = $(this);
        $.ajax({
            type: "POST",
            url: '/home/voteAction/ajaxAction',
            dataType: "json",
            data: {
                "id": id,
                "type": type
            },
            //contentType: 'application/json; charset=utf-8',
            async: true,
            success: function(data) {
                self.parent().children("span").html(data.nbVote);
                if(type == "up-vote"){
                    self.attr("src","img/up-arrow-orange.png");
                    self.parent().children(".down-vote").attr("src","img/down-arrow.png");
                }else{
                    self.attr("src","img/down-arrow-blue.png");
                    self.parent().children(".up-vote").attr("src","img/down-arrow.png");
                }
            }
        })
        });
});