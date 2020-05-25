'use strict';

var admin_helpers = {
    /**
     * 
     * @param {string} address - url для запроса.
     * @param {object|null} data - Данные в запросе. Если data == null, будет выполнен GET запрос, иначе, POST с содержимым
     */
    sendRequest(address, data){
        return new Promise((resolve, reject) => {
            let xhr = new XMLHttpRequest();

            xhr.open(data==null?'GET':'POST', address, true);
            xhr.responseType = 'json';

            xhr.onload = () => {
                if(xhr.status == 200){
                    resolve(xhr.response, xhr.status);
                }else{
                    reject(xhr.response, xhr.status);
                }
            };

            xhr.onerror = () => {
                reject(xhr.response, xhr.status);
            };

            xhr.send(data!=null?JSON.stringify(data):null);
        });
    },

    validatePageid(page_id) {
        let forbidden = [' ', '/', '\\', ':', '*', '?', '\"', '\'', '<', '>', '|'];
        if(!page_id || typeof page_id != 'string' || page_id.charAt(0) == '_' || !forbidden.every((char) => page_id.indexOf(char) == -1)){
            return false;
        }else{
            return true;
        }
    }
};


var popup = {
    /**
     * Private variable. Use setTransitionDuration.
     */
    transition_duration: 100,

    setTransitionDuration(milisec){
        document.documentElement.style.setProperty('--popup-transition-duration', (milisec/1000).toString()+'s');
        this.transition_duration = milisec;
    },

    showPopup(id){
        let popup = document.querySelector('.popup#'+id);
        if(popup){
            popup.classList.add('show');
        }else{
            throw new Error('Popup #'+id+' does not exist.');
        }
    },
    hidePopup(id){
        let popup = document.querySelector('.popup#'+id);
        if(popup){
            popup.classList.remove('show');
        }else{
            throw new Error('Popup #'+id+' does not exist.');
        }
    },
    isPopupShown(id){
        let popup = document.querySelector('.popup#'+id);
        if(popup){
            return popup.classList.contains('show');
        }else{
            throw new Error('Popup #'+id+' does not exist.');
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    popup.setTransitionDuration(150);

    document.querySelectorAll('.popup').forEach((elem) => {
        if(elem.id){
            elem.addEventListener('mousedown', (evt) => {
                if(evt.target === elem){
                    elem.classList.remove('show');
                }
            });
            elem.querySelectorAll('.close-button').forEach((btn) => {
                btn.addEventListener('click', () => {
                    elem.classList.remove('show');
                });
            });
        }
    });

    document.querySelectorAll('.content-tab-container').forEach((container) => {
        let tabs = {};
        let buttons = {};

        container.querySelectorAll('.content-tab').forEach((item) => {
            if('tabId' in item.dataset){
                tabs[item.dataset.tabId] = item;
            }
        });
        container.querySelectorAll('.content-tab-button').forEach((item) => {
            if('tabId' in item.dataset){
                buttons[item.dataset.tabId] = item;

                item.addEventListener('click', (evt) => {
                    if(item.dataset.tabId in tabs){
                        Object.keys(buttons).forEach((id) => { buttons[id].classList.remove('active'); });
                        Object.keys(tabs).forEach((id) => { tabs[id].classList.remove('show'); });
                        item.classList.add('active');
                        tabs[item.dataset.tabId].classList.add('show');
                    }
                });

                if(item.classList.contains('active')){
                    tabs[item.dataset.tabId].classList.add('show');
                }
            }
        });
    });

    document.querySelectorAll('.fold-header').forEach((elem) => {
        let fold = elem.parentElement;
        if(fold.classList.contains('fold')){
            elem.addEventListener('click', (evt) => {
                fold.classList.toggle('unfolded');
            });
        }
    });
});
