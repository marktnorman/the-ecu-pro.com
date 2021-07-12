// label as placeholder form js (lapForm)
const lapForm = document.querySelectorAll('[data-js-ref="lap-form"]')
const lapFormInput = document.querySelectorAll('[data-js-ref="lap-form-input"]')
const message = document.querySelector('[data-js-ref="lap-form-msg"]')
const lapFormSetLabelInUse = input => {
    const label = document.getElementById(input.getAttribute('data-js-label-id'))
    if (!input.value) {
        label.classList.remove('in-use')
    } else {
        label.classList.add('in-use')
    }
}
lapFormInput.forEach(input => {
    lapFormSetLabelInUse(input)
    input.addEventListener('focus', e => {
        document.getElementById(e.target.getAttribute('data-js-label-id')).classList.add('in-use')
    })
    input.addEventListener('blur', e => {
        lapFormSetLabelInUse(e.target)
    })
})
lapForm.forEach(form => {
    form.classList.add('is-ready')
    form.addEventListener('submit', e => {
        const submitBtn = e.target.querySelector('[data-js-ref="lap-form-submit"]')
        e.target.classList.add('is-submitting')
        submitBtn.setAttribute('disabled', true)
        submitBtn.innerText = 'Submitting'
        if (message) {
            message.remove()
        }
    })
})
if (message) {
    message.scrollIntoView()
}
