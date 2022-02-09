function onSubmit(form, button, defaultClass = true) {
    const elementForm = document.getElementById(form);
    const elementButton = document.getElementById(button);

    elementForm.addEventListener('submit', function () {
        elementButton.setAttribute('disabled', true);

        if (defaultClass) {
            elementButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span> Enviando Informações...</span>';
        }
    });
}

function categories() {
    const elements = {
        word: document.getElementById('words'),
        result: document.getElementById('results'),
        button: document.getElementById('create-btn'),
        title: document.getElementById('title'),
        field: document.getElementById('custom-fields'),
        block: document.getElementById('custom-fields-block')
    };

    elements.word.addEventListener('keyup', function () {
        fetch('/helpdesk/request/type/category', {
                method: 'POST',
                body: JSON.stringify({
                    words: this.value
                }),
                mode: 'cors',
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded'
                }
            })
            .then(response => response.json())
            .then(categories => {
                if (categories.result === false) {
                    let view = '<div class="message empty">';
                    view += '<div class="header">Ops!</div>';
                    view += `<div class="description">${categories.message}</div>`;
                    view += '</div>';

                    elements.result.innerHTML = view;
                    elements.result.style.display = 'block';
                    elements.button.setAttribute('disabled', true);
                    return;
                }

                let view = '<div class="visible">';

                categories.items.forEach(element => {

                    view += '<div class="category">';
                    view += `<div class="name">${element.departament}</div>`;
                    view += '<div class="results visible">';
                    view += `<a class="result text-reset text-decoration-none" id="item" data-category="${element.category_name}" data-subcategory="${element.sub_category}">`;
                    view += '<div class="content">';
                    view += `<div class="title">${element.category_name}</div>`;
                    view += `<div class="description">${element.category_description}</div>`;
                    view += '</div>';
                    view += '</a>';
                    view += '</div>';
                    view += '</div>';
                });

                view += '</div>';

                elements.result.innerHTML = view;

                const items = document.querySelectorAll('#item');

                items.forEach(items => {
                    items.addEventListener('click', function () {
                        const category = this.getAttribute('data-category');
                        const subcategory = this.getAttribute('data-subcategory');

                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'subcategory';
                        hidden.value = subcategory;

                        elements.block.appendChild(hidden);

                        fetch('/helpdesk/request/type/fields', {
                                method: 'POST',
                                body: JSON.stringify({
                                    id: subcategory
                                }),
                                mode: 'cors',
                                headers: {
                                    'Content-type': 'application/x-www-form-urlencoded'
                                }
                            })
                            .then(response => response.json())
                            .then(fields => {
                                if (fields.result === false) {
                                    console.log(`[HelpDesk][Field] - ${fields.message}`);
                                    elements.field.innerHTML = '';
                                    elements.block.classList.add('d-none');
                                    return;
                                }

                                fields.fields.forEach(field => {
                                    const div = document.createElement('div');
                                    div.className = 'form-group mb-2';
                            
                                    const input = document.createElement('input');
                                    input.type = 'text';
                                    input.name = field.field_name;
                                    input.id = field.field_name;
                                    input.setAttribute('required', field.field_required);
                                    input.setAttribute('autocomplete', 'off');
                                    input.className = 'form-control';


                                    const label = document.createElement('label');
                                    label.htmlFor = field.field_name;
                                    label.classList.add('form-label');

                                    if(field.field_required) {
                                        label.classList.add('required');
                                    }

                                    label.innerHTML = field.field_description + ':';

                                    div.appendChild(label);
                                    div.appendChild(input);
                                    elements.field.appendChild(div);
                                });

                                elements.block.classList.remove('d-none');
                            });

                        elements.word.value = category;
                        elements.title.value = category;
                        elements.result.style.display = 'none';
                    });
                });

                //elements.result.style.display = 'block';
                elements.button.removeAttribute('disabled');
            });
    });
}

function wordCount(field, div, max = 3000) {
    const count = document.getElementById(field);

    count.addEventListener('keyup', function () {
        let value = count.value.length;
        return div.innerHTML = max - value;
    });
}

function toggleMenu(event) {

    if (event.type === 'touchstart') {
        event.preventDefault();
    }

    const nav = document.getElementById('nav');

    nav.classList.toggle('active');

    const active = nav.classList.contains('active');

    event.currentTarget.setAttribute('aria-expanded', active);

    if (active) {
        event.currentTarget.setAttribute('aria-label', 'Abrir Menu');
    } else {
        event.currentTarget.setAttribute('aria-label', 'Fechar Menu');
    }


}

function execToggle() {
    const btn_mobile = document.getElementById('mobile-btn');

    btn_mobile.addEventListener('click', toggleMenu);
    btn_mobile.addEventListener('touchstart', toggleMenu);
}

function employee(id) {
    const entity = document.getElementById(id);
    
    entity.addEventListener('blur', function() {    
        fetch('/helpdesk/request/type/entity', {
            method: 'POST',
            body: JSON.stringify({
                entity: this.value
            }),
            mode: 'cors',
            headers: {'Content-type': 'application/x-www-form-urlencoded'}
        })
        .then(response => response.json())
        .then(data => {
            if (data.result === false) {
                window.alert(data.message);
                this.value = '';
                // console.log(data.message);
                return;
            }

            this.type = 'text';
            this.value = `${data.entity}  ${data.name}`;
            
        });
    });
}

