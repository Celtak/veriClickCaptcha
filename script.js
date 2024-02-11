(function main() {

    function isMobileOrTablet() {
        const userAgent = navigator.userAgent.toLowerCase();
        const isMobile = /iphone|ipad|ipod|android|blackberry|mini|windows\sce|palm/i.test(userAgent);
        if (isMobile) {
            return true; // L'utilisateur est sur un mobile ou une tablette
        } else {
            return false; // L'utilisateur est sur un ordinateur de bureau
        }
    }
    function captchaValider(ev) {

        captcha_valider.addEventListener(ev, () => {

            const patienter_captcha = document.getElementById('patienter_captcha');
            const imgs = sessionStorage.getItem('imgs');
            const data = new FormData();
            data.append('imgs', imgs);
    
            patienter_captcha.style.display = 'block';
            captcha_valider.style.display = 'none';
    
            fetch('index.php', {
                method: 'POST',
                body: data
            }).then(function (response) {
                return response.text();
            }).then(function (data) {
    
                const captcha = document.getElementById('captcha');
    
                if(data === 'Captcha validé') {
    
                    captcha.style.border = '0';
                    captcha.style.padding = '0';
    
                    captcha.innerHTML = "<div style='font-family: courier, sans-serif; font-family: courier;text-align:center;width:150px;padding: 5px;padding-left:10px;padding-right:10px;background-color: green;color: white;border-radius: 6px;margin:auto;margin-top: 20px;margin-bottom: 20px;'>Captcha validé</div>";
    
                } else {
    
                    fetch('index.php', {
                        method: 'GET',
                    }).then(function (response) {
                        return response.text();
                    }).then(function (data) {
    
                        // -- Supprimer le div parent_captcha
                        let div = document.createElement('div');
                        div.innerHTML = data;
                        div = div.querySelector('#parent_captcha');
                        // -- Fin
    
                        const parent_captcha = document.getElementById('parent_captcha');
                        parent_captcha.innerHTML = div.innerHTML;
                        
                        main();
    
                        // -- Afficher le message d'erreur
                        const erreur_captcha = document.getElementById('erreur_captcha');
                        erreur_captcha.style.display = 'block';
                        // -- Fin
    
                    }).catch(function (error) {
                        console.log(error);
                    });
                }
    
                patienter_captcha.style.display = 'none';
                captcha_valider.style.display = 'block';
    
            }).catch(function (error) {
                console.log(error);
            });
    
        } );

    }

    

    const img_captcha = document.querySelectorAll('.img_captcha');
    const captcha_valider = document.getElementById('captcha_valider');
    let imgs = [];
    img_captcha.forEach(function (img) {

        function imgCaptchaClick(ev) {

            img.addEventListener(ev, (e) => {
    
                if (e.target.style.border === '4px solid rgb(0, 0, 0)') {
                    e.target.style.border = '4px solid #cfcfcf';
                    e.target.backgroundColor = '#cfcfcf';
                    const title = e.target.getAttribute('data-title');
                    const index = imgs.indexOf(title);
                    imgs.splice(index, 1);
                    sessionStorage.setItem('imgs', imgs);
                } else {
                    const title = e.target.getAttribute('data-title');
                    e.target.style.border = '4px solid #000';
                    e.target.backgroundColor = '#000';
                    imgs.push(title);
                    sessionStorage.setItem('imgs', imgs);
                }
    
    
    
            });
    
        }

        if(isMobileOrTablet()) {

            imgCaptchaClick('touchstart');

        } else {

            imgCaptchaClick('click');

        }

    });



    if(isMobileOrTablet()) {

        captchaValider('touchstart');

    } else {

        captchaValider('click');

    }

    
})();