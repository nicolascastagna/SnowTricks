const addVideoButton = document.getElementById("add-video");
const videoList = document.getElementById("videos-list");

addVideoButton.addEventListener("click", () => {
    const index = videoList.querySelectorAll("input").length;
    const newWidget = videoList.dataset.prototype.replace(/__name__/g, index);
    videoList.insertAdjacentHTML("beforeend", newWidget);
});
