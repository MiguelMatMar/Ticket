document.addEventListener('DOMContentLoaded', () => {
    let menuItems = document.querySelectorAll('.menu-item.has-sub');

    menuItems.forEach(item => {
        let parentBox = item.querySelector('.parent-box');
        
        parentBox.addEventListener('click', (e) => {
            e.preventDefault();

            let subMenu = item.querySelector('.sub-menu');
            let angleDown = item.querySelector('.angle-down');
            let angleUp = item.querySelector('.angle-up');
            let isOpen = !subMenu.classList.contains('nonvisible');

            // 1. Cerramos todos los demás menús primero
            closeAllMenus();

            // 2. Si el que clicamos no estaba abierto, lo abrimos
            if (!isOpen) {
                item.classList.add('active');
                subMenu.classList.remove('nonvisible');
                angleDown.classList.add('nonvisible');
                angleUp.classList.remove('nonvisible');
            }
        });
    });

    function closeAllMenus() {
        menuItems.forEach(item => {
            item.classList.remove('active');
            
            let subMenu = item.querySelector('.sub-menu');
            let angleDown = item.querySelector('.angle-down');
            let angleUp = item.querySelector('.angle-up');

            subMenu.classList.add('nonvisible');
            angleDown.classList.remove('nonvisible');
            angleUp.classList.add('nonvisible');
        });
    }

    let sidebar = document.querySelector('.left-bar');
    let toggleBtn = document.querySelector('.desplegarSideBar');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });
    }
});