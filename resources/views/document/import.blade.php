<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Importar Arquivo JSON</div>
                    <div class="card-body">
                        <form id="importForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="json_file">Selecione um arquivo JSON para importar:</label>
                                <input type="file" class="form-control-file" name="json_file" id="json_file">
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">Importar</button>
                            </div>
                        </form>
                    </div>
                    <div id="message-success"></div>
                    <div id="message-error" style="color:red"></div>
                </div>
            </div>
        </div>
    </div>
</body>
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

    document.getElementById('importForm').addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData();
        formData.append('json_file', document.getElementById('json_file').files[0]);

        axios.post('{{ route('document.import') }}', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then(response => {
            handleSuccess(response, 'message-success');
        }).catch(error => {
            handleError(error, 'message-error');
        });
    });
</script>
</html>
