'use strict';

class Constructor {
    constructor(page_id){
        this.page_id = page_id;
        this.page = {};
        this.modules = [];
        this.form = undefined;
    }

    importPage(page){
        this.page = page;
    }

    importModules(modules){
        this.modules = modules;
    }

    savePage(){
        return this.sendPostData('/constructor/page/'+this.page_id+'/update', this.page);
    }

    sendPostData(address, data){
        return new Promise((resolve, reject) => {
            let xhr = new XMLHttpRequest();

            xhr.open('POST', address, true);
            xhr.responseType = 'json';

            xhr.onload = () => {
                resolve(xhr.response);
            };

            xhr.onerror = () => {
                reject(xhr.status);
            };

            //xhr.send(JSON.stringify(data));
            setTimeout(() => resolve('null'), 1000);
        });
    }

    init(){
        this.form = document.getElementById('constructor-page-edit-form');
        this.form.onsubmit = (evt) => {
            evt.preventDefault();
            if(this.updatePageFromForm()){
                this.savePage().then(() => {location.reload();}); 
            }
        };
    }

    updatePageFromForm(){
        if(!this.form){
            return false;
        }

        return true;
    }

    update
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.constructor-button').forEach((item) => {
        switch(item.dataset.action){
            case 'show-popup':
                item.addEventListener('click', (evt) => {
                    if(evt.target === item){
                        document.getElementById(item.dataset.target).classList.add('show');
                        document.getElementById('constructor-popup-background').classList.add('show');
                    }
                });
                break;
            case 'hide-popups':
                item.addEventListener('click', (evt) => {
                    if(evt.target === item){
                        document.getElementById('constructor-popup-background').classList.remove('show');
                        document.querySelectorAll('.constructor-popup .popup').forEach((popup) => {popup.classList.remove('show');});
                    }
                });
                break;
        }
    });

    document.querySelectorAll('.block-edit [name^="block-active-"]').forEach((elem) => {
        let blockname = elem.name.substr('block-active-'.length);
        elem.addEventListener('change', (evt) => {
            if(evt.target.value == 'true'){
                document.getElementById('block-edit-'+blockname).classList.remove('no-params');
            }else{
                document.getElementById('block-edit-'+blockname).classList.add('no-params');
            }
        });
    });

    document.querySelectorAll('.block-edit.fold .unfold-button').forEach((item) => {
        if(item.dataset.target){
            item.addEventListener('click', () => {
                document.getElementById(item.dataset.target).classList.toggle('unfolded');
            });
        }
    });

    constructor.init();
});


