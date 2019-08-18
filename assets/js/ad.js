// ici ont appaele en jquery les functionalite de ajout ou de modifier 

// ICI on fait appele pour les deux formulaire d'eddition et creation de formulaire


// on se place sur le button d 'ajout d'image explique lui que on veut une function particuliere
$('#add-image').click(function() {
    // un fois a l'interieur je vu recupere les nr d'images que il ya 

    // je recupere le numero des futurs champs que je vais créer 
    // le + devant se pour dire que ce que va mais recupere cette expresion $('#widgets-counter').val(); serra une nombre et pas un chaine de caractérée
    const index = +$('#widgets-counter').val();

    // Je recupére le prototype des entrées
    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g, index);

    // Je injecte ce code au sein de la div pour ajouter une images
    // dans la div d'images je veux ajouter mon tpml = templaete
    $('#ad_images').append(tmpl);

    // ici on demande de ajouter 1 au compteur pour pas avoir de dublon 
    $('#widgets-counter').val(index + 1);

    // Je gére le button supprimer
    handleDeleteButtons();
});

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function() {
        const target = this.dataset.target;
        $(target).remove();
    });
}

// function count l'index des images, pour ajouter ou supprime des images 
function updateCounter() {
    // le + devant se pour dire que ce que va mais recupere cette expresion $('#widgets-counter').val(); serra une nombre et pas un chaine de caractérée
    const count = +$('#ad_images div.form-group').length;

    $('#widget-counter').val(count);
}

// ici on appele la function updateCounter()
updateCounter();

handleDeleteButtons();