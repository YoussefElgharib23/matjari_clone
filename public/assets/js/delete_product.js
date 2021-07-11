$(document).ready(() => {

    $('._delete_product_link').each((i, link) => {
        $(link).click((e) => {

            e.preventDefault()

            let $form = $(`form#${$(link).data('form')}`)

            $form.submit()

        })
    })

})