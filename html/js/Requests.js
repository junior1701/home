class Requests {
    static form;
    static setform(id) {
        this.form = document.getElementById(id);
        if (!this.form) {
            throw new Error("O formulário não foi encontrado!");
        }
        return this;
    }
    static async Post(url) {
        const formData = new FormData(this.form);
        const option = {
            method: 'POST',
            body: formData,
            cache: 'default',
            mode: 'cors'
        };
        const response = await fetch(url, option);
        return await response.json();
    }
}
export { Requests };

