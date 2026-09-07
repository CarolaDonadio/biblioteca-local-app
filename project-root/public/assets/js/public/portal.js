document.addEventListener('DOMContentLoaded', () => {

    const menuBtn = document.querySelector('.pub-menu-btn');
    const nav = document.querySelector('.pub-nav');

    if (!menuBtn || !nav) {
        return;
    }

    menuBtn.addEventListener('click', () => {

        const abierto = nav.classList.toggle('pub-nav--abierto');

        menuBtn.setAttribute(
            'aria-expanded',
            abierto ? 'true' : 'false'
        );

    });

});