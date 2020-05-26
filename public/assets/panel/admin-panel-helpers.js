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
    },

    initPopup(elem){
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
    }
};

function queryDirectChildrenAll(elm, sel, first = false){
    var ret = [], i = 0, l = elm.childNodes.length;
    for (var i; i < l; ++i){
        let ch = elm.childNodes[i];
        if ((ch.matches && ch.matches(sel)) || (ch.matchesSelector && ch.matchesSelector(sel))){
            if(first){
                return elm.childNodes[i];
            }else{
                ret.push(elm.childNodes[i]);
            }
        }
    }
    if(first){
        return null;
    }else{
        return ret;
    }
}

function queryDirectChildFirst(elm, sel){
    return queryDirectChildrenAll(elm, sel, true);
}

let tabs = {
    tabButtonClickHandler(evt){
        let item = evt.target;
        if('tabId' in item.dataset){
            let buttons = item.parentNode.parentNode;
            let container = buttons.parentNode;
            let tabs = queryDirectChildFirst(container, '.content-tabs');

            if(!buttons || !container || !tabs){
                return;
            }

            buttons.querySelectorAll('.content-tab-button').forEach((elem) => {elem.classList.remove('active');});
            queryDirectChildrenAll(tabs, '.content-tab').forEach((elem) => {
                if('tabId' in elem.dataset && elem.dataset.tabId == item.dataset.tabId){
                    elem.classList.add('show'); 
                }else{
                    elem.classList.remove('show'); 
                }
            });
            item.classList.add('active');
        }
    },

    initTabContainer(container){
        let buttons = queryDirectChildFirst(container, '.content-tab-buttons');
        buttons.querySelectorAll('.content-tab-button').forEach((item) => {
            item.addEventListener('click', tabs.tabButtonClickHandler);
            if(item.classList.contains('active')){
                item.click();
            }
        });
    },
};

let folds = {
    foldHeaderClickHandler(evt){
        evt.target.parentNode.classList.toggle('unfolded');
    },

    initFold(fold){
        queryDirectChildFirst(fold, '.fold-header').addEventListener('click', folds.foldHeaderClickHandler);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    popup.setTransitionDuration(150);

    document.querySelectorAll('.popup').forEach((elem) => {popup.initPopup(elem)});
    document.querySelectorAll('.content-tab-container').forEach((elem) => {tabs.initTabContainer(elem)});
    document.querySelectorAll('.fold').forEach((elem) => {folds.initFold(elem);});
});
