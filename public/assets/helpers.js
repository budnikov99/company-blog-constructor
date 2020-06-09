'use strict';

var helpers = {
    /**
     * 
     * @param {string} address - url для запроса.
     * @param {object|null} data - Данные в запросе. Если data == null, будет выполнен GET запрос, иначе, POST с содержимым
     */
    sendRequest(address, data, headers = {'Content-Type': 'application/json'}){
        return new Promise((resolve, reject) => {
            let xhr = new XMLHttpRequest();

            xhr.open(data==null?'GET':'POST', address, true);

            xhr.responseType = 'json';

            if(headers){
                Object.keys(headers).forEach((item) => {
                    xhr.setRequestHeader(item, headers[item]);
                });
            }

            xhr.onload = () => {
                if(xhr.status == 200){
                    resolve({'data': xhr.response, 'status': xhr.status});
                }else{
                    reject({'data': xhr.response, 'status': xhr.status});
                }
            };

            xhr.onerror = () => {
                reject({'data': null, 'status': 0});
            };

            xhr.send(data);
        });
    },

    findGetParameter(parameterName) {
        var result = null,
            tmp = [];
        location.search
            .substr(1)
            .split("&")
            .forEach(function (item) {
              tmp = item.split("=");
              if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
            });
        return result;
    },

    wrap(elements, classes){
        if(!elements){ elements = []; }
        if(!classes){ classes = []; }
        if(!Array.isArray(classes)){ classes = [classes]; }
        if(!Array.isArray(elements)){ elements = [elements]; }
        let wrapper = document.createElement('div');
        classes.forEach((classname) => { wrapper.classList.add(classname); });
        elements.forEach((element) => {
            if(!element){return;}
            if(typeof element == 'string'){ element = document.createTextNode(element); }
            wrapper.appendChild(element);
        });
        return wrapper;
    },

    transliterateRu(title = ''){
        let letters = {
            'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd',
            'е': 'e', 'ё': 'jo', 'ж': 'zh', 'з': 'z', 'и': 'i',
            'й': 'j', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n',
            'о': 'o', 'п': 'p', 'р': 'r', 'с': 's', 'т': 't',
            'у': 'y', 'ф': 'f', 'х': 'x', 'ц': 'ts', 'ч': 'tch',
            'ш': 'sh', 'щ': 'sch', 'ъ': '', 'ы': 'y', 'ь': '',
            'э': 'e', 'ю': 'ju', 'я': 'ja'
        };
        let keep = 'abcdefghijklmnopqrstuvwxyz1234567890-'.split('');

        return title.toLowerCase().split('').map((symbol) => 
            keep.indexOf(symbol) == -1?(letters.hasOwnProperty(symbol)?letters[symbol]:'_'):symbol
        ).join('');
    },
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
            if(elem.classList.contains('close-button')){
                elem.addEventListener('mousedown', (evt) => {
                    if(evt.target === elem){
                        elem.classList.remove('show');
                    }
                });
            }
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
    selectTab(container, tab_id){
        if(!container){
            return;
        }

        let buttons = queryDirectChildFirst(container, '.tab-buttons');
        let tabs = queryDirectChildFirst(container, '.tabs');

        if(!buttons || !tabs){
            return;
        }

        buttons.querySelectorAll('.tab-button').forEach((elem) => {
            if('tabId' in elem.dataset && elem.dataset.tabId == tab_id){
                elem.classList.add('active'); 
            }else{
                elem.classList.remove('active'); 
            }
        });
        queryDirectChildrenAll(tabs, '.tab').forEach((elem) => {
            if('tabId' in elem.dataset && elem.dataset.tabId == tab_id){
                elem.classList.add('show'); 
            }else{
                elem.classList.remove('show'); 
            }
        });
    },

    tabButtonClickHandler(evt){
        let item = evt.target;
        if('tabId' in item.dataset){
            let container = item.parentNode;
            while(container && !container.classList.contains('tab-container')){
                container = container.parentNode;
            }   

            tabs.selectTab(container, item.dataset.tabId);            
        }
    },

    initTabContainer(container){
        let buttons = queryDirectChildFirst(container, '.tab-buttons');
        buttons.querySelectorAll('.tab-button').forEach((item) => {
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
    document.querySelectorAll('.tab-container').forEach((elem) => {tabs.initTabContainer(elem)});
    document.querySelectorAll('.fold').forEach((elem) => {folds.initFold(elem);});
});
