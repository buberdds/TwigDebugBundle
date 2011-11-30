
function TDToggleTemplateExtraInfo(uuid){
    var display = document.getElementById(uuid).style.display;
    document.getElementById(uuid).style.display = display == 'none' ? 'block' : 'none';
    return false;
}

function TDToggleTemplatesBlocks(uuid){
    var el = document.getElementById(uuid).parentNode.parentNode;
    document.getElementById(uuid).parentNode.parentNode.className = (el.className.indexOf('hideBlock') != -1) ? el.className.replace(/hideBlock/,'showBlock') : el.className.replace(/showBlock/,'hideBlock');
}