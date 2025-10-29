document.querySelector("#myForm").addEventListener("submit", async function (e) {
  e.preventDefault();

  let formData = new FormData(this);
  let messageBox = document.querySelector("#message");

  messageBox.textContent = "Sending...";

  try {
    let response = await fetch("https://yourphpbackend.epizy.com/api.php", {
      method: "POST",
      body: formData
    });

    let result = await response.text();
    messageBox.textContent = result;
  } catch (error) {
    messageBox.textContent = "Error sending data.";
    console.error(error);
  }
});
