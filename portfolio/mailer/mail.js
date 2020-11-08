const forms = document.querySelectorAll('form');
const recaptchaSiteKey = '';

forms.forEach(form => prepearForm(form));
forms.forEach( form => form.addEventListener('submit', formSend));

/* подготовка формы */
function prepearForm (form) {
	const recaptcha = form.querySelector('.g-recaptcha');
	const dataName = form.querySelectorAll('[data-name]');
	const inputFile = form.querySelector('[for="file"]');
	const groupData = form.querySelector('[data-form-group]');
	const formElements = [];

	/* добавляет атрибут ключа recaptcha в обертку div.g-recaptcha*/
	recaptcha.setAttribute('data-sitekey', recaptchaSiteKey);

	/* если есть for="file" вставить верстку input type="file" */
	if (inputFile) {
		inputFile.insertAdjacentHTML(
			"afterbegin",
			`<input 
				type='file' 
				id="${form.id}-attachments" 
				data-name='attachments[]' 
				multiple
				max-size="2000">`
		);
	}

	/* создаем массив из всех элементов формы */
	for (key in form.elements) {
		if (!isNaN(key) ) {
			const dataName = form.elements[key].dataset.name;
			form.elements[key].setAttribute('name', dataName);
			formElements.push(form.elements[key]);
		}
	}

	/* группируем данные из data-form-group в массив */
	if (groupData) {
		const groupName = groupData.dataset.formGroup;
		const groupInputs = groupData.querySelectorAll('input');
		groupInputs.forEach(input => {
			input.setAttribute('name', `${groupName}[]`);
			input.setAttribute('value', input.id);
		});
	}
}

/* отправка формы */
function formSend (e) {
	e.preventDefault();
	send(this);
}

/* отправка */
async function send (form) {
	const doneMessage = form.parentNode.querySelector('.w-form-done');
	const failMessage = form.parentNode.querySelector('.w-form-fail');
	const formData = new FormData(form);

	/* добавляем имя формы */
	formData.append('Форма', form.dataset.name);

	const response = await fetch('wp-content/themes/portfolio/mailer/mail.php', {
		method: 'POST',
		body: formData
	});

	// const result = await response.text();
	// console.log(result);

	const result = await response.json();
	const errors = result.errors;
	const success = result.success;

	/* проверка на ошибки */
	if (errors) {
		failMessage.innerHTML = '';
		errors.forEach( error => failMessage.insertAdjacentHTML('afterbegin', `<div>${error}</div>`));
		failMessage.style.display = 'block';
		setTimeout(() => {
			failMessage.style.display = 'none';
		}, 6000);
	}

	/* сообщение об отправке */
	if (success) {
		doneMessage.innerHTML = '';
		doneMessage.insertAdjacentHTML('afterbegin', `<div>Сообщение отправлено!</div>`);
		doneMessage.style.display = 'block';
		setTimeout(() => {
			doneMessage.style.display = 'none';
		}, 3000);
		form.reset();
	}
	grecaptcha.reset();
}
