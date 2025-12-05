import { Validate } from "./Validate.js";
import { Requests } from "./Requests.js";
const Salvar = document.getElementById('insert');

$('#cpf').inputmask({ "mask": ["999.999.999-99", "99.999.999/9999-99"] });
$('#tel').inputmask({ "mask": ["(99) 99999-9999"] });

Salvar.addEventListener('click', async () => {
    Validate.SetForm('form').Valid();
    const response = await Requests.SetForm('form').Post('/cliente/insert');
    console.log(response);
     if (!response.status) {
       Swal.fire({
            icon: 'error',
            title: 'Por favor, preencha todos os campos obrigatórios.',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        return;
    }

    Swal.fire({
        title: "Cadastro realizado com sucesso!",
        icon: "success",
        draggable: true
    });

});