<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/contact.css">
    <title>Contacto</title>
</head>
<body>
    <section class="contact-section">
    <div class="contact-container">
        <div class="contact-info-column">
            <p class="subtitle">Estamos aqui para ajudar voce!</p>
            <h2 class="contact-title">
            Fale com a <br>Kindergarten
            </h2>
            <p class="contact-text">
                A nossa equipa está sempre disponível para esclarecer dúvidas, orientar o processo de matrícula 
                e fornecer todas as informações sobre o funcionamento do Jardim de Infância Kindergarten. 
                Entre em contacto connosco por qualquer um dos canais abaixo.
            </p>
            <div class="contact-details">
                <div class="contact-item">
                    <i class="fas fa-envelope icon-mail"></i>
                    <span>E-mail</span>
                    <p class="detail-value">Kindergarten@gmail.com</p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone-alt icon-phone"></i>
                    <span>Numero</span>
                    <p class="detail-value">+258 847852208</p>
                </div>
            </div>
        </div>
        <div class="form-column-wrapper">
    <form class="contact-form" id="contactFormSite">
        <label for="name">Nome</label>
        <input type="text" id="name" name="name" placeholder="Jaime Fernando" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="jaime@example.com" required>

        <label for="message">Mensagem</label>
        <textarea id="message" name="message" rows="4" placeholder="Escreva a mensagem" required></textarea>
        
        <div id="formMessage" style="margin-top: 10px; display: none;"></div>
        
        <button type="submit" class="submit-button" id="submitBtn">
            Enviar
        </button>
    </form>
</div>


    </div>
</section>
<script>
document.getElementById('contactFormSite').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    const formMessage = document.getElementById('formMessage');
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    submitBtn.disabled = true;
    formMessage.style.display = 'none';
    
    const formData = new FormData(this);

    fetch('api/enviar_contato_site.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        formMessage.style.display = 'block';
        
        if (data.success) {
            formMessage.innerHTML = '<div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; border: 1px solid #c3e6cb;">' +
                                   '<i class="fas fa-check-circle"></i> ' + data.message + '</div>';
            document.getElementById('contactFormSite').reset();
        } else {
            formMessage.innerHTML = '<div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; border: 1px solid #f5c6cb;">' +
                                   '<i class="fas fa-exclamation-triangle"></i> ' + data.message + '</div>';
        }
        
    
        setTimeout(() => {
            formMessage.style.opacity = '0';
            setTimeout(() => {
                formMessage.style.display = 'none';
                formMessage.style.opacity = '1';
            }, 500);
        }, 5000);
    })
    .catch(error => {
        formMessage.style.display = 'block';
        formMessage.innerHTML = '<div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;">' +
                               '<i class="fas fa-exclamation-triangle"></i> Erro ao enviar. Tente novamente.</div>';
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>
</body>
</html>