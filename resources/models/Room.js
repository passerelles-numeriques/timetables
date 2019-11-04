export default class Room {
  constructor(name = '', id = 0, googleId = '') {
    this.name = name;
    this.id = id;
    this.googleId = googleId;
  }

  equals(room) {
    return this.id === room.id &&
      this.name === room.name &&
      this.googleId === room.googleId;
  }
}
