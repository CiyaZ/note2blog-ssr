window.onload = function () {
    // treeview初始化
    let treeView = document.getElementById('tree-view');
    Array.from(treeView.getElementsByClassName("collapsible")).forEach(function (collapse) {
        collapse.addEventListener("click", function () {
            toggle_tree_node(collapse);
        }, false);
    });



};

function toggle_tree_node(node) {
    let menuNode = node.nextSibling;
    if(menuNode.style.display === 'block') {
        menuNode.style.display = 'none';
    } else {
        menuNode.style.display = 'block';
    }
}

function toggle_modal() {
    let modal = document.getElementById('modal');
    if (modal.style.visibility === 'visible') {
        modal.style.visibility = 'hidden';
        document.body.className = document.body.className.replace(' modal-open', '');
    }
    else {
        modal.style.visibility = 'visible';
        document.body.className = document.body.className + ' modal-open';
    }
}
