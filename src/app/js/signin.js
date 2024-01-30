document.getElementById("signin-form").addEventListener("submit", function(event) {
  event.preventDefault(); // Empêche la soumission du formulaire par défaut

  // Récupère les valeurs des champs email et password
  var email = document.getElementById("email").value;
  var password = document.getElementById("password").value;

  // Validation côté client
  if (!email || !password) {
      alert("Veuillez remplir tous les champs.");
      return;
  }

  // Soumet le formulaire via AJAX
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "signin.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function() {
      if (xhr.readyState === XMLHttpRequest.DONE) {
          if (xhr.status === 200) {
              // Réponse reçue avec succès, redirige vers la page d'accueil
              window.location.href = "index.php";
          } else {
              // Erreur lors de la soumission du formulaire, affiche un message d'erreur
              alert(xhr.responseText);
          }
      }
  };
  xhr.send("email=" + encodeURIComponent(email) + "&password=" + encodeURIComponent(password));
});
