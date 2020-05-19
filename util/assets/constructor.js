'use strict';

class Constructor {
    constructor(page_id){
        this.page_id = page_id;
    }

    sendPostData(address, data){
        return new Promise((resolve, reject) => {
            xhr = new XMLHttpRequest();

            xhr.open('POST', address, true);
            xhr.responseType = 'json';

            xhr.onload = () => {
                resolve(xhr.response);
            };

            xhr.onerror = () => {
                reject();
            };

            xhr.send(JSON.stringify(data));
        });
    }

};




