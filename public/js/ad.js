
var compteur = $("#annonce_images .row").lenght;

$("#add_image").click(function () // on appel le button add_image et on lui dit de reagir au click via une fonction anonyme

{


    var tmpl = $("#annonce_images").data("prototype"); //on recupere le contenu de l'id "annonce_images" et le data-prototype;
    // on recupere (le prototype) l'information du contenu ds une variable en cliquant sur le bouton
    // console.log(tmpl);

    tmpl = tmpl.replace(/__name__/g, compteur++)  //le "g" est un flag(drapeau) remplace toute les occurence qui va trouver du nom : __name__
    // console.log(tmpl);

    $("#annonce_images").append(tmpl);

    deleteBlock();

});

function deleteBlock() {
    $('.del_image').click(function () {

        var id = $(this).data("bloc");   //on recupere l'id de la div (row)
        //console.log(id);


        $('#' + id).remove();

    });





}
deleteBlock();