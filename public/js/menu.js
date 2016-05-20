
function showThisSubMenuHeader(obj,id){
//    alert(obj.child.offsetLeft);

//    alert($(obj).children().width());
    var mLeft=$(obj).children().position().left;

    $("#"+id).css("left",mLeft-0);
//    $(obj).children("div").offset=$(obj).children().offset();
    $("#"+id).show();
    $(obj).children("li").addClass("headNavA");
    $(obj).children("li").children("a").css("color","#FFFFFF");
//    $(obj).children().children("a").addClass("menuiconahover");
//    document.getElementById(id).style.display="block";
}

function closeThisSubMenuHeader(obj,id){
    $(obj).children("li").removeClass("headNavA");
    $(obj).children("li").children("a").css("color","#555555");
    $("#"+id).hide();
}


function headMenu(id, title, url,obj) {

}