<form action="/import" method="post" enctype="multipart/form-data">
    @csrf
    <button type="submit">Iniciar Importação</button>
</form>

<div id="message-success"></div>
<div id="message-error" style="color:red"></div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    function handleSuccess(response, elementId) {
        const successMessage = response.data.message;
        const messageElement = document.getElementById(elementId);
        messageElement.innerText = successMessage;
    }

    function handleError(error, elementId) {
        const errorMessage = error.response.data.message;
        const messageElement = document.getElementById(elementId);
        messageElement.innerText = errorMessage;
    }

    document.querySelector('form').addEventListener('submit', function (event) {
        event.preventDefault();

        axios.post('{{ route('document.import') }}', {}, {
            headers: { 'Content-Type': 'multipart/form-data' }
        }).then(response => {
            handleSuccess(response, 'message-success');
        }).catch(error => {
            handleError(error, 'message-error');
        });
    });
</script>
