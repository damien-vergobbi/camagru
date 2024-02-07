// Sélectionnez la balise vidéo et le bouton de capture
const videoElement = document.getElementById('videoElement');
const imageElement = document.getElementById('imageElement');
const videoContainer = document.getElementById('video_container');
const captureButton = document.getElementById('captureButton');
const logError = document.getElementById('log_error');
const sendLoader = document.getElementById('sendLoader');

const stopVideoStream = () => {
  if (videoElement.srcObject) {
    const tracks = videoElement.srcObject.getTracks();
    tracks.forEach(track => track.stop());
    videoElement.srcObject = null;
  }
}

const startVideoStream = () => {
  if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
    if (logError) logError.innerHTML = 'Your browser does not support the camera feature. Please use a modern browser or click on "Add image".';
    return;
  }

  navigator.mediaDevices.getUserMedia({ video: true })
  .then((stream) => {
    videoElement.srcObject = stream;
    videoElement.play();
  })
  .catch((error) => {
    if (logError) logError.innerHTML = 'Please allow access to your camera and microphone to use this feature or click on "Add image".';
  });
}

// Fonction pour convertir une data URL en objet Blob
function dataURLtoBlob(dataURL) {
  const parts = dataURL.split(';base64,');
  const contentType = parts[0].split(':')[1];
  const raw = window.atob(parts[1]);
  const rawLength = raw.length;
  const uInt8Array = new Uint8Array(rawLength);

  for (let i = 0; i < rawLength; ++i) {
      uInt8Array[i] = raw.charCodeAt(i);
  }

  return new Blob([uInt8Array], { type: contentType });
}

const uploadImage = ({
  backgroundSettings,
  stickerSettings,
}) => {
  try {
    // Hide capture button and show loader
    captureButton.classList.add('hidden');
    sendLoader.classList.remove('hidden');

    const xhr = new XMLHttpRequest();

    // Create new file and send it to the server
    const formData = new FormData();
    formData.append('backgroundWidth', backgroundSettings.width);
    formData.append('backgroundHeight', backgroundSettings.height);
    formData.append('backgroundX', backgroundSettings.x);
    formData.append('backgroundY', backgroundSettings.y);
    formData.append('stickerWidth', stickerSettings.width);
    formData.append('stickerHeight', stickerSettings.height);
    formData.append('stickerX', stickerSettings.x);
    formData.append('stickerY', stickerSettings.y);

    formData.append('backgroundData', dataURLtoBlob(backgroundSettings.base64), 'background.png');
    formData.append('stickerData', dataURLtoBlob(stickerSettings.base64), 'sticker.png');

    xhr.open('POST', '/scripts/upload.php', true);
    xhr.onreadystatechange = function() {
      try {
        if (xhr.readyState === XMLHttpRequest.UNSENT) {
          // Error
          logError.innerHTML = "An error occurred. Please try again.";
          return;
        }

        if (xhr.readyState === XMLHttpRequest.DONE) {
          if (xhr.status !== 200) {
            // Error
            logError.innerHTML = "An error occurred. Please try again.";
            return;
          }

          const text = JSON.parse(xhr.responseText);

          if (text?.path) {
            // Success
            const aElt = document.createElement('a');
            aElt.href = text.url;

            const imageElt = new Image();
            imageElt.src = text.path;
            imageElt.width = 200;
            imageElt.height = 150;

            // Add link to the image
            aElt.appendChild(imageElt);
            
            const imageContainer = document.getElementById('previous_images');

            // Remove <p> tag if no image
            if (imageContainer.querySelector('p')) {
              imageContainer.querySelector('p').remove();
            }

            // Append before the first child
            imageContainer.insertBefore(aElt, imageContainer.firstChild);
          } else {
            // Error
            throw new Error(JSON.parse(xhr.responseText).error || xhr.responseText);
          }

          // Show capture button and hide loader
          captureButton.classList.remove('hidden');
          sendLoader.classList.add('hidden');
        }
      } catch (error) {
        // Error
        if (error?.message) {
          logError.innerHTML = error?.message;
        }

        // Show capture button and hide loader
        captureButton.classList.remove('hidden');
        sendLoader.classList.add('hidden');
      }
    };
    xhr.onerror = function(error) {
      logError.innerHTML = "An error occurred. Please try again.";

      // Show capture button and hide loader
      captureButton.classList.remove('hidden');
      sendLoader.classList.add('hidden');
    }
    xhr.send(formData);
  } catch (error) {
    logError.innerHTML = "An error occurred. Please try again.";

    // Show capture button and hide loader
    captureButton.classList.remove('hidden');
    sendLoader.classList.add('hidden');
  }
};

// Start video stream when videoElement is loaded
document?.addEventListener('DOMContentLoaded', () => {
  // Check if video exists
  if (videoElement)
    startVideoStream();
});

captureButton?.addEventListener('click', () => {
  // Check if video is recording or image is displayed
  if (imageElement.classList.contains('hidden') && videoElement.srcObject === null) {
    logError.innerHTML = 'Please take a picture or select an image before taking a picture';
    return;
  }

  // Reset log error
  logError.innerHTML = '';

  const backgroundSettings = {
    width: videoElement.classList.contains('hidden') ? imageElement.width : videoElement.offsetWidth,
    height: videoElement.classList.contains('hidden') ? imageElement.height : videoElement.offsetHeight,
    x: 0,
    y: 0,
    base64: ''
  };

  const stickerSettings = {
    width: stickerElement.width,
    height: stickerElement.height,
    x: stickerElement.offsetLeft,
    y: stickerElement.offsetTop,
    base64: ''
  };

  // Convert background to base64
  const canvas = document.createElement('canvas');
  canvas.width = backgroundSettings.width;
  canvas.height = backgroundSettings.height;
  const context = canvas.getContext('2d');

  if (videoElement.classList.contains('hidden')) {
    context.drawImage(imageElement, 0, 0, canvas.width, canvas.height);
  } else {
    context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
  }

  const dataURL = canvas.toDataURL('image/png');
  backgroundSettings.base64 = dataURL;

  // Convert sticker to base64
  const stickerCanvas = document.createElement('canvas');
  stickerCanvas.width = stickerSettings.width;
  stickerCanvas.height = stickerSettings.height;
  const stickerContext = stickerCanvas.getContext('2d');
  stickerContext.drawImage(stickerElement, 0, 0, stickerCanvas.width, stickerCanvas.height);

  const stickerDataURL = stickerCanvas.toDataURL('image/png');
  stickerSettings.base64 = stickerDataURL;

  // Upload image
  uploadImage({
    backgroundSettings,
    stickerSettings,
  });
});

const imageBack = () => {
  // Hide video
  videoElement.classList.add('hidden');
  imageElement.classList.remove('hidden');

  document.getElementById('delImageButton').classList.remove('hidden');
  document.getElementById('upImageButton').classList.add('hidden');

  // Reset log error
  logError.innerHTML = '';

  // Stop video stream
  stopVideoStream();
}

const videoBack = () => {
  // Hide video
  videoElement.classList.remove('hidden');
  imageElement.classList.add('hidden');

  document.getElementById('delImageButton').classList.add('hidden');
  document.getElementById('upImageButton').classList.remove('hidden');

  // Reset log error
  logError.innerHTML = '';

  // Start video stream
  startVideoStream();
}

// Delete image
document.getElementById('delImageButton')?.addEventListener('click', () => {
  imageElement.classList.add('hidden');
  imageElement.src = '';
  videoBack();
});

// Move the sticker
let offsetX = 0;
let offsetY = 0;
let isDragging = false;

function startDrag(event) {
  isDragging = true;
  offsetX = event.clientX - stickerElement.getBoundingClientRect().left;
  offsetY = event.clientY - stickerElement.getBoundingClientRect().top;
}

function drag(event) {
  if (isDragging) {
    if (videoElement.classList.contains('hidden')) {
      const imageRect = imageElement.getBoundingClientRect();
      let x = event.clientX - imageRect.left - offsetX;
      let y = event.clientY - imageRect.top - offsetY;

      x = Math.max(0, Math.min(imageElement.offsetWidth - stickerElement.offsetWidth, x));
      y = Math.max(0, Math.min(imageElement.offsetHeight - stickerElement.offsetHeight, y));

      stickerElement.style.left = `${x}px`;
      stickerElement.style.top = `${y}px`;
    } else {
      const videoRect = videoElement.getBoundingClientRect();
      let x = event.clientX - videoRect.left - offsetX;
      let y = event.clientY - videoRect.top - offsetY;

      x = Math.max(0, Math.min(videoElement.offsetWidth - stickerElement.offsetWidth, x));
      y = Math.max(0, Math.min(videoElement.offsetHeight - stickerElement.offsetHeight, y));

      stickerElement.style.left = `${x}px`;
      stickerElement.style.top = `${y}px`;
    }
  }
}

const toggleDrag = (event) => {
  // Check if video is recording or image is displayed
  if (imageElement.classList.contains('hidden') && videoElement.srcObject === null) {
    logError.innerHTML = 'Please take a picture or select an image before moving the sticker';
    return;
  }
  
  isDragging = !isDragging;

  if (isDragging) {
    stickerElement.style.cursor = 'grabbing';
    offsetX = event.clientX - stickerElement.getBoundingClientRect().left;
    offsetY = event.clientY - stickerElement.getBoundingClientRect().top;
  } else {
    stickerElement.style.cursor = 'grab';
  }
}


// Print the sticker checked
const stickerChecked = document.querySelector('#stickers_list input:checked');
const stickerElement = document.getElementById('stickerElement');

if (stickerChecked && stickerElement) {
  stickerElement.src = `../app/stickers/${stickerChecked.value}`;
  stickerElement.style.display = 'block';

  // Center the sticker
  stickerElement.style.left = `${videoContainer.offsetWidth / 2 - stickerElement.offsetWidth / 2}px`;
  stickerElement.style.top = `${videoContainer.offsetHeight / 2 - stickerElement.offsetHeight / 2}px`;

  // Handle input name="sticker" change
  document.querySelectorAll('#stickers_list input').forEach(input => {
    input.addEventListener('change', (event) => {
      const selectedSticker = event.target.value;
      const stickerElement = document.getElementById('stickerElement');

      stickerElement.src = `../app/stickers/${selectedSticker}`;
      stickerElement.style.display = 'block';

      // Center the sticker
      stickerElement.style.left = `${videoContainer.offsetWidth / 2 - stickerElement.offsetWidth / 2}px`;
      stickerElement.style.top = `${videoContainer.offsetHeight / 2 - stickerElement.offsetHeight / 2}px`;
    });
  });

  stickerElement.addEventListener('click', toggleDrag);
  stickerElement.addEventListener('mousemove', drag);
}

/* Upload sticker section */
const stickerFile = document.getElementById('stickerFile');
const upStickerButton = document.getElementById('upStickerButton');

upStickerButton?.addEventListener('click', () => {
  stickerFile.click();
});

stickerFile?.addEventListener('change', (event) => {
  const file = event.target.files[0];

  if (!file) return;

  // Vérification de la taille du fichier
  const maxSizeInBytes = 10 * 1024 * 1024; // 10 Mo
  if (file.size > maxSizeInBytes) {
    logError.innerHTML = 'The file is too large. Please select a file less than 10MB.';
    return;
  }

  // Vérification du type de fichier
  const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
  if (!allowedTypes.includes(file.type)) {
    logError.innerHTML = 'The file type is not allowed. Please select an image file.';
    return;
  }

  // Uncheck all stickers
  document.querySelectorAll('#stickers_list input').forEach(input => {
    input.checked = false;
  });

  const reader = new FileReader();

  reader.onload = (e) => {
    stickerElement.src = e.target.result;
    stickerElement.style.display = 'block';

    if (stickerElement.naturalWidth !== 0 && stickerElement.naturalHeight !== 0) {
      // Centrer le sticker
      stickerElement.style.left = `${videoContainer.offsetWidth / 2 - stickerElement.offsetWidth / 2}px`;
      stickerElement.style.top = `${videoContainer.offsetHeight / 2 - stickerElement.offsetHeight / 2}px`;
    } else {
      logError.innerHTML = 'The file is not a valid image. Please select another file.';
      stickerElement.style.display = 'none';
    }
  };

  reader.onerror = (e) => {
    logError.innerHTML = 'An error occurred while reading the file. Please try again.';
  };

  // Lecture du fichier en tant que Data URL
  reader.readAsDataURL(file);
});

// Listen sticker on error
stickerElement?.addEventListener('error', () => {
  logError.innerHTML = 'An error occurred while reading the file. Please try again.';
  stickerElement.style.display = 'none';
});

/* Upload image section */
const imageFile = document.getElementById('imageFile');
const upImageButton = document.getElementById('upImageButton');

upImageButton?.addEventListener('click', () => {
  imageFile.click();
});

imageFile?.addEventListener('change', (event) => {
  const file = event.target.files[0];

  if (!file) return;

  // Vérification de la taille du fichier
  const maxSizeInBytes = 10 * 1024 * 1024; // 10 Mo
  if (file.size > maxSizeInBytes) {
    logError.innerHTML = 'The file is too large. Please select a file less than 10MB.';
    return;
  }

  // Vérification du type de fichier
  const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
  if (!allowedTypes.includes(file.type)) {
    logError.innerHTML = 'The file type is not allowed. Please select an image file.';
    return;
  }

  const reader = new FileReader();

  reader.onload = (e) => {
    // Hide video and show image
    videoElement.classList.add('hidden');
    imageElement.src = e.target.result;
    imageElement.classList.remove('hidden');

    imageBack();
  };

  reader.onerror = (e) => {
    videoBack();
    logError.innerHTML = 'An error occurred while reading the file. Please try again.';
  };

  // Lecture du fichier en tant que Data URL
  reader.readAsDataURL(file);
});

// Listen image on error
imageElement?.addEventListener('error', () => {
  if (imageElement.classList.contains('hidden')) return;

  videoBack();
  logError.innerHTML = 'An error occurred while reading the file. Please try again.';
});


