export default class Class {
  constructor(name = '', id = 0, googleId = '') {
    this.name = name;
    this.id = id;
    this.googleId = googleId;
  }

  equals(classRoom) {
    return this.id === classRoom.id &&
      this.name === classRoom.name &&
      this.googleId === classRoom.googleId;
  }
}
