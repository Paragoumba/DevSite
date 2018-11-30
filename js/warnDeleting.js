function warnDeleting(id){

    var delCheckbox = document.getElementById('delCheckbox');
    var delSpan = document.getElementById('delSpan');
    var formElt = document.getElementById(id);

    if (delCheckbox !== null && delCheckbox.checked && delSpan.parentElement === formElt) return true;
    else {

        var inputElt = document.createElement('input');
        var spanElt = document.createElement('span');

        if (delSpan !== null) delSpan.remove();

        spanElt.id = 'delSpan';
        inputElt.type = 'checkbox';
        inputElt.id = 'delCheckbox';
        spanElt.appendChild(inputElt);
        spanElt.insertBefore(document.createTextNode('Supprimer ?'), inputElt.nextSibling);
        formElt.appendChild(spanElt);

        return false;

    }
}