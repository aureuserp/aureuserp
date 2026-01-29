document.addEventListener('DOMContentLoaded', function() {
    const updateHash = function (id) {
        window.location.hash = `#${id}`;
    };

    const navButton = document.getElementById('nav-button');
    const menuWrapper = document.querySelector('.tocify-wrapper');
    function toggleSidebar(event) {
        event.preventDefault();
        if (menuWrapper) {
            menuWrapper.classList.toggle('open');
            navButton.classList.toggle('open');
        }
    }
    function closeSidebar() {
        if (menuWrapper) {
            menuWrapper.classList.remove('open');
            navButton.classList.remove('open');
        }
    }
    navButton.addEventListener('click', toggleSidebar);

    window.hljs.highlightAll();

    const wrapper = document.getElementById('toc');
    // https://jets.js.org/
    window.jets = new window.Jets({
        // *OR - Selects elements whose values contains at least one part of search substring
        searchSelector: '*OR',
        searchTag: '#input-search',
        contentTag: '#toc li',
        didSearch: function(term) {
            wrapper.classList.toggle('jets-searching', String(term).length > 0)
        },
        // map these accent keys to plain values
        diacriticsMap: {
            a: 'ÀÁÂÃÄÅàáâãäåĀāąĄ',
            c: 'ÇçćĆčČ',
            d: 'đĐďĎ',
            e: 'ÈÉÊËèéêëěĚĒēęĘ',
            i: 'ÌÍÎÏìíîïĪī',
            l: 'łŁ',
            n: 'ÑñňŇńŃ',
            o: 'ÒÓÔÕÕÖØòóôõöøŌō',
            r: 'řŘ',
            s: 'ŠšśŚ',
            t: 'ťŤ',
            u: 'ÙÚÛÜùúûüůŮŪū',
            y: 'ŸÿýÝ',
            z: 'ŽžżŻźŹ'
        }
    });

    function hashChange() {
        const currentItems = document.querySelectorAll('.tocify-subheader.visible, .tocify-item.tocify-focus');
        Array.from(currentItems).forEach((elem) => {
            elem.classList.remove('visible', 'tocify-focus');
        });

        const currentTag = document.querySelector(`a[href="${window.location.hash}"]`);
        if (currentTag) {
            const parent = currentTag.closest('.tocify-subheader');
            if (parent) {
                parent.classList.add('visible');
            }

            const siblings = currentTag.closest('.tocify-header');
            if (siblings) {
                Array.from(siblings.querySelectorAll('.tocify-subheader')).forEach((elem) => {
                    elem.classList.add('visible');
                });
            }

            currentTag.parentElement.classList.add('tocify-focus');

            // wait for dom changes to be done
            setTimeout(() => {
                currentTag.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
                // only close the sidebar on level-2 events
                if (currentTag.parentElement.classList.contains('level-2')) {
                    closeSidebar();
                }
            }, 1500);
        }
    }

    let languages = JSON.parse(document.body.getAttribute('data-languages'));
    // Support a key => value object where the key is the name, or an array of strings where the value is the name
    if (!Array.isArray(languages)) {
        languages = Object.values(languages);
    }
    // if there is no language use the first one
    const currentLanguage = window.localStorage.getItem('language') || languages[0];
    const languageStyle = document.getElementById('language-style');
    const langSelector = document.querySelectorAll('.lang-selector button.lang-button');

    function setActiveLanguage(newLanguage) {
        window.localStorage.setItem('language', newLanguage);
        if (!languageStyle) {
            return;
        }

        const newStyle = languages.map((language) => {
            return language === newLanguage
                // the current one should be visible
                ? `body .content .${language}-example pre { display: block; }`
                // the inactive one should be hidden
                : `body .content .${language}-example pre { display: none; }`;
        }).join(`\n`);

        Array.from(langSelector).forEach((elem) => {
            elem.classList.toggle('active', elem.getAttribute('data-language-name') === newLanguage);
        });

        const activeHash = window.location.hash.slice(1);

        languageStyle.innerHTML = newStyle;

        setTimeout(() => {
            updateHash(activeHash);
        }, 200);
    }

    setActiveLanguage(currentLanguage);

    Array.from(langSelector).forEach((elem) => {
        elem.addEventListener('click', () => {
            const newLanguage = elem.getAttribute('data-language-name');
            setActiveLanguage(newLanguage);
        });
    });

    window.addEventListener('hashchange', hashChange, false);

    const divs = document.querySelectorAll('.content h1[id], .content h2[id]');

    document.addEventListener('scroll', () => {
        divs.forEach(item => {
            const rect = item.getBoundingClientRect();
            if (rect.top > 0 && rect.top < 150) {
                const location = window.location.toString().split('#')[0];
                history.replaceState(null, null, location + '#' + item.id);
                hashChange();
            }
        });
    });

    hashChange();
});
