export const getUserImage = (image) => {
    // Handle user image URL logic here
    return image ? '/user-images/' + image : '/user-images/default-user-image.jpg';
};

export const getGroupImage = (image) => {
    // Handle group image URL logic here
    return image ? '/group-images/' + image : '/group-images/default-group-img.jpg';
};