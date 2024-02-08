document.addEventListener('DOMContentLoaded', function () {
    const commentEditButtons = document.querySelectorAll('.js-comment-edit')

    if (commentEditButtons.length > 0) {
        commentEditButtons.forEach((button) => {
            button.addEventListener('click', function (e) {
                e.preventDefault()

                const comment = e.currentTarget.closest('.comment')

                // Hide all opened forms
                document.querySelectorAll('.comment form').forEach((el) => el.classList.add('hidden'))

                const body = comment.querySelector('.comment__body')
                const form = comment.querySelector('form')

                body.classList.add('hidden')
                form.classList.remove('hidden')

                comment.querySelector('.js-cancel-comment-edit').addEventListener('click', function () {
                    body.classList.remove('hidden')
                    form.classList.add('hidden')
                })
            })
        })
    }
})
