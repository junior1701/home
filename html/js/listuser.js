import { Requests } from "./Requests.js";

async function deletar(id) {
    document.getElementById('id').value = id;
    const response = await Requests.SetForm('form').Post('/usuario/delete');
    console.log(response);

}
window.deletar = deletar;